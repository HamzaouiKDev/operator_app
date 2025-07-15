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
        // --- 1. Définitions Centralisées des Statuts ---
        $statutsComplets = ['Complet', 'termine'];
        $statutsPartiels = ['Partiel', 'réponse partielle'];
        $statutsRefus = ['refus', 'refus final'];
        $statutsImpossible = ['impossible de contacter'];
        $statutRendezVous = 'un rendez-vous';
        $statutsAAppeler = ['à appeler'];
        
        // Statuts considérés comme "traités" pour la performance
        $statutsTraites = array_merge(
            $statutsComplets, $statutsPartiels, $statutsRefus, 
            $statutsImpossible, [$statutRendezVous], $statutsAAppeler
        );

        // --- 2. KPIs Généraux (Calculs directs et clairs) ---
        $totalTeleoperateurs = User::role('Téléopérateur')->count();
        $totalEchantillons = EchantillonEnquete::count();
        $rendezVousAujourdhui = RendezVous::whereDate('heure_rdv', Carbon::today())->count();
        
        // Le total des "terminés" inclut les complets, refus et impossibles
        $echantillonsTermines = EchantillonEnquete::whereIn('statut', array_merge($statutsComplets, $statutsRefus, $statutsImpossible))->count();
        
        // Le total des "partiels" sont ceux qui ont ACTUELLEMENT ce statut
        $totalPartiels = EchantillonEnquete::whereIn('statut', $statutsPartiels)->count();

        // Pour distinguer les RDV, on identifie les échantillons qui ont eu un statut partiel dans leur historique
        $echantillonsAvecHistoriquePartielIds = DB::table('echantillon_statut_histories')
            ->whereIn('nouveau_statut', $statutsPartiels)
            ->distinct('echantillon_enquete_id')
            ->pluck('echantillon_enquete_id');

        $rdvAvecPartiel = EchantillonEnquete::where('statut', $statutRendezVous)
            ->whereIn('id', $echantillonsAvecHistoriquePartielIds)
            ->count();
            
        $rdvSansPartiel = EchantillonEnquete::where('statut', $statutRendezVous)
            ->whereNotIn('id', $echantillonsAvecHistoriquePartielIds)
            ->count();
            
        // --- 3. Performance des Téléopérateurs (Requête unique et optimisée) ---
        $teleoperateurs = User::role('Téléopérateur')
            ->withCount([
                'echantillons as echantillons_traites_count' => fn($q) => $q->whereIn('statut', $statutsTraites),
                'echantillons as echantillons_complets_count' => fn($q) => $q->whereIn('statut', $statutsComplets),
                'echantillons as echantillons_partiels_count' => fn($q) => $q->whereIn('statut', $statutsPartiels),
                'rendezVous as rdv_avec_partiel_count' => fn($q) => $q->whereIn('echantillon_enquete_id', $echantillonsAvecHistoriquePartielIds),
                'rendezVous as rdv_sans_partiel_count' => fn($q) => $q->whereNotIn('echantillon_enquete_id', $echantillonsAvecHistoriquePartielIds),
            ])
            ->get();
            
        // --- 4. Données pour le Graphique (Logique simplifiée et cohérente) ---
        $statsBrutes = EchantillonEnquete::select('statut', DB::raw('count(*) as total'))->groupBy('statut')->pluck('total', 'statut');

        $statutsPourGraphique = [
            'غير معالج' => EchantillonEnquete::whereNull('utilisateur_id')->count(),
            'في الانتظار' => $statsBrutes->get('en attente', 0),
            'مكتمل' => $statsBrutes->only($statutsComplets)->sum(),
            'رد جزئي' => $statsBrutes->only($statutsPartiels)->sum(), // Utilise la donnée brute actuelle
            'رفض' => $statsBrutes->only($statutsRefus)->sum(),
            'إستحالة الإتصال' => $statsBrutes->only($statutsImpossible)->sum(),
            'موعد (مع جزئي)' => $rdvAvecPartiel, // Utilise le KPI déjà calculé
            'موعد (بدون جزئي)' => $rdvSansPartiel, // Utilise le KPI déjà calculé
            'إعادة إتصال' => $statsBrutes->get('à appeler', 0),
        ];

        // Filtrer les statuts avec un compte de zéro pour ne pas encombrer le graphique
        $statutsFiltres = array_filter($statutsPourGraphique, fn($value) => $value > 0);

        $chartLabels = array_keys($statutsFiltres);
        $chartData = array_values($statutsFiltres);

        // --- 5. Envoyer les données à la vue ---
        return view('admin.dashboard.index', compact(
            'totalTeleoperateurs', 'totalEchantillons', 'rendezVousAujourdhui',
            'echantillonsTermines', 'totalPartiels', 'rdvAvecPartiel', 'rdvSansPartiel',
            'chartLabels', 'chartData', 'teleoperateurs'
        ));
    }
}
