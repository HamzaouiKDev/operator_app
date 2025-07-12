<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RendezVous;
use App\Models\EchantillonEnquete;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    /**
     * Affiche le tableau de bord du superviseur avec les statistiques de l'opérateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // --- 1. Définitions et Pré-calculs (Logique de l'Admin Dashboard) ---
        $statutsComplets = ['Complet', 'termine'];
        $statutsPartiels = ['Partiel', 'réponse partielle'];
        $statutsRefus = ['refus', 'refus final'];
        $statutRendezVous = 'un rendez-vous';
        $statutsImpossible = ['impossible de contacter'];
        $statutsAAppeler = ['à appeler'];
        $historyStatusColumnName = 'nouveau_statut';

        $teleoperateurs = User::role('Téléopérateur')->orderBy('name')->get();
        $viewData = ['teleoperateurs' => $teleoperateurs];

        $historyModelClass = get_class((new EchantillonEnquete())->statusHistories()->getRelated());

        // IDs des échantillons ayant un historique partiel
        $echantillonsAvecHistoriquePartielIds = $historyModelClass::query()
            ->whereIn($historyStatusColumnName, $statutsPartiels)
            ->distinct('echantillon_enquete_id')
            ->pluck('echantillon_enquete_id');

        // IDs des échantillons complétés
        $completedEchantillonIds = EchantillonEnquete::query()
            ->whereIn('statut', $statutsComplets)
            ->orWhereHas('statusHistories', function ($query) use ($statutsComplets, $historyStatusColumnName) {
                $query->whereIn($historyStatusColumnName, $statutsComplets);
            })
            ->pluck('id');

        // --- 2. Calcul pour l'Avancement Général (Barres de Progression) ---
        $nombreTotalEchantillons = EchantillonEnquete::count();
        $avancementParStatut = [];

        if ($nombreTotalEchantillons > 0) {
            // Calculs précis basés sur la logique de l'admin
            $comptesComplets = EchantillonEnquete::whereIn('statut', $statutsComplets)->count();
            $comptesPartiels = $historyModelClass::query()->whereIn($historyStatusColumnName, $statutsPartiels)->whereNotIn('echantillon_enquete_id', $completedEchantillonIds)->distinct('echantillon_enquete_id')->count();
            $comptesRdvAvecPartiel = EchantillonEnquete::where('statut', $statutRendezVous)->whereIn('id', $echantillonsAvecHistoriquePartielIds)->count();
            $comptesRdvSansPartiel = EchantillonEnquete::where('statut', $statutRendezVous)->whereNotIn('id', $echantillonsAvecHistoriquePartielIds)->count();
            $comptesRefus = EchantillonEnquete::whereIn('statut', $statutsRefus)->count();
            $comptesAAppeler = EchantillonEnquete::whereIn('statut', $statutsAAppeler)->count();
            $comptesImpossible = EchantillonEnquete::whereIn('statut', $statutsImpossible)->count();

            $totalTraite = $comptesComplets + $comptesPartiels + $comptesRdvAvecPartiel + $comptesRdvSansPartiel + $comptesRefus + $comptesAAppeler + $comptesImpossible;
            $enAttenteCount = $nombreTotalEchantillons - $totalTraite;

            $statutsConfig = [
                ['label' => 'مكتمل', 'count' => $comptesComplets, 'couleur' => '#10b981'],
                ['label' => 'رد جزئي', 'count' => $comptesPartiels, 'couleur' => '#a78bfa'],
                ['label' => 'موعد (مع جزئي)', 'count' => $comptesRdvAvecPartiel, 'couleur' => '#1abc9c'],
                ['label' => 'موعد (بدون جزئي)', 'count' => $comptesRdvSansPartiel, 'couleur' => '#95a5a6'],
                ['label' => 'رفض', 'count' => $comptesRefus, 'couleur' => '#ef4444'],
                ['label' => 'إعادة إتصال', 'count' => $comptesAAppeler, 'couleur' => '#f97316'],
                ['label' => 'إستحالة الإتصال', 'count' => $comptesImpossible, 'couleur' => '#64748b'],
                ['label' => 'في الانتظار', 'count' => $enAttenteCount > 0 ? $enAttenteCount : 0, 'couleur' => '#e5e7eb'],
            ];

            foreach ($statutsConfig as $config) {
                $avancementParStatut[] = [
                    'nom' => $config['label'],
                    'count' => $config['count'],
                    'pourcentage' => ($config['count'] / $nombreTotalEchantillons) * 100,
                    'couleur' => $config['couleur'],
                ];
            }
        }
        $viewData['avancementParStatut'] = $avancementParStatut;

        // --- 3. Calculs pour un Opérateur Spécifique ---
        $teleoperateurId = $request->input('teleoperateur_id');
        if ($teleoperateurId) {
            $selectedTeleoperateur = User::findOrFail($teleoperateurId);
            if (!$selectedTeleoperateur->hasRole('Téléopérateur')) {
                abort(403, 'L\'utilisateur sélectionné n\'est pas un téléopérateur.');
            }

            $viewData['selectedTeleoperateur'] = $selectedTeleoperateur;

            // KPIs de performance
            $statutsTraites = ['Complet', 'termine', 'Partiel', 'réponse partielle', 'refus', 'impossible de contacter', 'un rendez-vous', 'à appeler', 'répondu'];

            $statsOperateur = User::where('id', $teleoperateurId)->withCount([
                'echantillons as echantillons_traites' => fn($q) => $q->whereIn('statut', $statutsTraites),
                'echantillons as echantillons_complets' => fn($q) => $q->whereIn('statut', $statutsComplets),
                'rendezVous as rdv_avec_partiel_count' => fn($q) => $q->whereIn('echantillon_enquete_id', $echantillonsAvecHistoriquePartielIds),
                'rendezVous as rdv_sans_partiel_count' => fn($q) => $q->whereNotIn('echantillon_enquete_id', $echantillonsAvecHistoriquePartielIds),
            ])->first();

            // Logique de calcul pour les partiels de l'opérateur
            $partielsActuels = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)
                ->whereIn('statut', $statutsPartiels)
                ->count();

            $partielsHistorique = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)
                ->whereNotIn('statut', $statutsPartiels)
                ->whereNotIn('statut', $statutsComplets)
                ->whereHas('statusHistories', function ($query) use ($statutsPartiels, $historyStatusColumnName) {
                    $query->whereIn($historyStatusColumnName, $statutsPartiels);
                })
                ->whereDoesntHave('statusHistories', function ($query) use ($statutsComplets, $historyStatusColumnName) {
                    $query->whereIn($historyStatusColumnName, $statutsComplets);
                })
                ->count();
            
            $statsOperateur->echantillons_partiels = $partielsActuels + $partielsHistorique;
            $viewData['statsOperateur'] = $statsOperateur;

            // Données pour le graphique d'évolution
            $chartData = ['labels' => [], 'completedData' => [], 'partialData' => []];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $chartData['labels'][] = $date->locale('fr')->translatedFormat('D d/m');
                
                // ✅ CORRECTION: Utilisation de whereBetween pour une requête de date plus robuste
                $startDate = $date->copy()->startOfDay();
                $endDate = $date->copy()->endOfDay();
                
                $chartData['completedData'][] = $historyModelClass::where('user_id', $teleoperateurId)
                    ->whereIn($historyStatusColumnName, $statutsComplets)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->distinct('echantillon_enquete_id')
                    ->count();

                $chartData['partialData'][] = $historyModelClass::where('user_id', $teleoperateurId)
                    ->whereIn($historyStatusColumnName, $statutsPartiels)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->distinct('echantillon_enquete_id')
                    ->count();
            }
            $viewData['evolutionChartData'] = $chartData;
        }

        return view('supervisor.dashboard', $viewData);
    }
}
