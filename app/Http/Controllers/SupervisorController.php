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
        // Récupère les téléopérateurs pour le menu déroulant
        $teleoperateurs = User::role('Téléopérateur')->orderBy('name')->get();
        $viewData = ['teleoperateurs' => $teleoperateurs];

        // --- DÉBUT : NOUVEAU CALCUL POUR L'AVANCEMENT GÉNÉRAL PAR STATUT ---
        $nombreTotalEchantillons = EchantillonEnquete::count();
        
        // Obtenir le compte pour chaque statut
        $comptesParStatut = EchantillonEnquete::query()
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $avancementParStatut = [];
        $totalTraite = 0;

        $statutsConfig = [
            ['noms' => ['Complet', 'termine'], 'label' => 'مكتمل', 'couleur' => '#28a745'],
            ['noms' => ['Partiel'], 'label' => 'مكتمل جزئيا', 'couleur' => '#ffc107'],
            ['noms' => ['un rendez-vous'], 'label' => 'موعد', 'couleur' => '#17a2b8'],
            ['noms' => ['refus', 'refus final'], 'label' => 'رفض', 'couleur' => '#dc3545'],
            ['noms' => ['à appeler'], 'label' => 'إعادة إتصال', 'couleur' => '#007bff'],
            ['noms' => ['impossible de contacter'], 'label' => 'إستحالة الإتصال', 'couleur' => '#6c757d'],
        ];

        foreach ($statutsConfig as $config) {
            $count = 0;
            foreach ($config['noms'] as $nom) {
                $count += $comptesParStatut->get($nom, 0);
            }
            
            if ($nombreTotalEchantillons > 0) {
                $pourcentage = ($count / $nombreTotalEchantillons) * 100;
                $avancementParStatut[] = [
                    'nom' => $config['label'],
                    'count' => $count,
                    'pourcentage' => $pourcentage,
                    'couleur' => $config['couleur'],
                ];
            }
            $totalTraite += $count;
        }

        // Calculer les échantillons "En attente"
        $enAttenteCount = $nombreTotalEchantillons - $totalTraite;
        if ($nombreTotalEchantillons > 0) {
             $avancementParStatut[] = [
                'nom' => 'في الانتظار',
                'count' => $enAttenteCount,
                'pourcentage' => ($enAttenteCount / $nombreTotalEchantillons) * 100,
                'couleur' => '#e9ecef',
            ];
        }
       
        $viewData['avancementParStatut'] = $avancementParStatut;
        // --- FIN : NOUVEAU CALCUL ---


        $teleoperateurId = $request->input('teleoperateur_id');

        if ($teleoperateurId) {
            $selectedTeleoperateur = User::findOrFail($teleoperateurId);

            if (!$selectedTeleoperateur->hasRole('Téléopérateur')) {
                abort(403, 'L\'utilisateur sélectionné n\'est pas un téléopérateur.');
            }

            $viewData['selectedTeleoperateur'] = $selectedTeleoperateur;
            
            // --- Calculs des Statistiques Détaillées par Opérateur ---
            $enqueteIds = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)->pluck('id');
            $statutsTraites = ['Complet', 'termine', 'Partiel', 'refus', 'refus final', 'un rendez-vous', 'impossible de contacter'];

            // KPIs
            $viewData['nombreEntreprisesAttribuees'] = count($enqueteIds);
            $viewData['totalRendezVous'] = RendezVous::whereIn('echantillon_enquete_id', $enqueteIds)->count();
            $viewData['echantillonsTraites'] = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)->whereIn('statut', $statutsTraites)->count();
            
            $nombreEchantillonsComplets = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)->whereIn('statut', ['Complet', 'termine'])->count();
            $viewData['tauxDefficacite'] = ($viewData['nombreEntreprisesAttribuees'] > 0) ? round(($nombreEchantillonsComplets / $viewData['nombreEntreprisesAttribuees']) * 100, 2) : 0;

            // Tables
            $viewData['entreprisesParStatut'] = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)->groupBy('statut')->selectRaw('statut, count(*) as count')->pluck('count', 'statut');
            $viewData['statutsAujourdhui'] = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)->whereDate('updated_at', Carbon::today())->groupBy('statut')->selectRaw('statut, count(*) as count')->pluck('count', 'statut');
            
            // Chart
            $chartData = ['labels' => [], 'completedData' => [], 'partialData' => []];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $chartData['labels'][] = $date->locale('fr')->translatedFormat('D d/m');
                $chartData['completedData'][] = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)->whereIn('statut', ['Complet', 'termine'])->whereDate('updated_at', $date)->count();
                $chartData['partialData'][] = EchantillonEnquete::where('utilisateur_id', $teleoperateurId)->where('statut', 'Partiel')->whereDate('updated_at', $date)->count();
            }
            $viewData['evolutionChartData'] = $chartData;
        }

        return view('supervisor.dashboard', $viewData);
    }
}
