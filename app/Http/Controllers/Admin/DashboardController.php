<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EchantillonEnquete;
use App\Models\RendezVous;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // --- 1. Définition claire et centralisée des statuts ---
        $statutsComplets = ['Complet', 'termine'];
        $statutsPartiels = ['Partiel', 'réponse partielle'];
        $statutsRefus = ['refus'];
        $statutsImpossible = ['impossible de contacter'];
        $statutRendezVous = 'un rendez-vous';

        $statutsTermines = array_merge($statutsComplets, $statutsRefus, $statutsImpossible);
        $statutsTraites = ['Complet', 'termine', 'Partiel', 'réponse partielle', 'refus', 'impossible de contacter', 'un rendez-vous', 'à appeler', 'répondu'];
        $historyStatusColumnName = 'nouveau_statut';

        // --- 2. KPIs Généraux ---
        $totalTeleoperateurs = User::role('Téléopérateur')->count();
        $totalEchantillons = EchantillonEnquete::count();
        $rendezVousAujourdhui = RendezVous::whereDate('heure_rdv', Carbon::today())->count();
        $echantillonsTermines = EchantillonEnquete::whereIn('statut', $statutsTermines)->count();

        // Informations sur le modèle d'historique
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

        // Total des partiels (non attribué)
        $totalPartiels = $historyModelClass::query()
            ->whereIn($historyStatusColumnName, $statutsPartiels)
            ->whereNotIn('echantillon_enquete_id', $completedEchantillonIds)
            ->distinct('echantillon_enquete_id')
            ->count();
            
        // KPIs globaux pour les RDV
        $rdvAvecPartiel = EchantillonEnquete::where('statut', $statutRendezVous)
            ->whereIn('id', $echantillonsAvecHistoriquePartielIds)
            ->count();
        $rdvSansPartiel = EchantillonEnquete::where('statut', $statutRendezVous)
            ->whereNotIn('id', $echantillonsAvecHistoriquePartielIds)
            ->count();

        // --- 3. Données pour le Graphique ---
        $statsBrutes = EchantillonEnquete::query()->select('statut', DB::raw('count(*) as total'))->groupBy('statut')->pluck('total', 'statut');
        $nonTraites = EchantillonEnquete::whereNull('utilisateur_id')->count();
        $statutsADiscuter = [
            'non traité' => 'غير معالج',
            'en attente' => 'في الانتظار',
            'Complet' => 'مكتمل',
            'Partiel' => 'رد جزئي',
            'refus' => 'رفض',
            'impossible de contacter' => 'إستحالة الإتصال',
            'rdv_avec_partiel' => 'موعد (مع جزئي)',
            'rdv_sans_partiel' => 'موعد (بدون جزئي)',
            'à appeler' => 'إعادة إتصال',
        ];

        $chartLabels = [];
        $chartData = [];
        foreach ($statutsADiscuter as $key => $label) {
            $count = 0;
            if ($key === 'non traité') { $count = $nonTraites; }
            elseif ($key === 'Complet') { $count = $statsBrutes->only($statutsComplets)->sum(); }
            elseif ($key === 'Partiel') { $count = $totalPartiels; }
            elseif ($key === 'rdv_avec_partiel') { $count = $rdvAvecPartiel; }
            elseif ($key === 'rdv_sans_partiel') { $count = $rdvSansPartiel; }
            else { $count = $statsBrutes->get($key, 0); }
            $chartLabels[] = $label;
            $chartData[] = $count;
        }

        // --- 4. Performance des téléopérateurs ---
        $teleoperateurs = User::role('Téléopérateur')
            ->withCount([
                'echantillons as echantillons_traites' => fn($q) => $q->whereIn('statut', $statutsTraites),
                'echantillons as echantillons_complets' => fn($q) => $q->whereIn('statut', $statutsComplets),
                'rendezVous as rdv_avec_partiel_count' => fn($q) => $q->whereIn('echantillon_enquete_id', $echantillonsAvecHistoriquePartielIds),
                'rendezVous as rdv_sans_partiel_count' => fn($q) => $q->whereNotIn('echantillon_enquete_id', $echantillonsAvecHistoriquePartielIds),
            ])
            ->get();

        // --- 5. LOGIQUE D'ATTRIBUTION DES PARTIELS (Logique corrigée et juste) ---
        // On crédite chaque opérateur pour chaque ÉCHANTILLON UNIQUE qu'il a passé en statut partiel.
        $partielsParOperateur = $historyModelClass::query()
            ->select('user_id', DB::raw('count(distinct echantillon_enquete_id) as count'))
            ->whereIn($historyStatusColumnName, $statutsPartiels)
            ->whereNotIn('echantillon_enquete_id', $completedEchantillonIds) // On exclut les échantillons déjà complétés.
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->pluck('count', 'user_id'); // Renvoie une collection [user_id => count]

        // On assigne manuellement le compte de partiels à chaque opérateur.
        foreach ($teleoperateurs as $teleoperateur) {
            $teleoperateur->echantillons_partiels = $partielsParOperateur->get($teleoperateur->id, 0);
        }

        return view('admin.dashboard.index', compact(
            'totalTeleoperateurs', 'totalEchantillons', 'rendezVousAujourdhui',
            'echantillonsTermines', 'totalPartiels', 'rdvAvecPartiel', 'rdvSansPartiel',
            'chartLabels', 'chartData', 'teleoperateurs'
        ));
    }
}
