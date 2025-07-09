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
        // --- KPIs (Indicateurs Clés de Performance) ---
        $totalTeleoperateurs = User::role('Téléopérateur')->count();
        $totalEchantillons = EchantillonEnquete::count();
        $rendezVousAujourdhui = RendezVous::whereDate('heure_rdv', Carbon::today())->count();

        // --- ✅ NOUVELLE LOGIQUE POUR LE GRAPHIQUE ET LES STATS ---

        // 1. On récupère le décompte de chaque statut pour TOUS les échantillons en une seule requête.
        $statsBrutes = EchantillonEnquete::query()
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // ✅ 2. On calcule le nombre d'échantillons "Non traités" séparément.
        $nonTraites = EchantillonEnquete::whereNull('utilisateur_id')->count();

        // 3. On définit la liste complète des statuts à afficher et leur traduction.
        $statutsADiscuter = [
            'non traité' => 'غير معالج', // ✅ Statut ajouté
            'en attente' => 'في الانتظار',
            'Complet' => 'مكتمل',
            'Partiel' => 'رد جزئي',
            'refus' => 'رفض',
            'impossible de contacter' => 'إستحالة الإتصال',
            'un rendez-vous' => 'موعد',
            'à appeler' => 'إعادة إتصال',
        ];

        // 4. On prépare les données pour le graphique.
        $chartLabels = [];
        $chartData = [];

        foreach ($statutsADiscuter as $key => $label) {
            $count = 0;

            if ($key === 'non traité') {
                $count = $nonTraites; // ✅ On utilise le compte calculé plus haut
            } elseif ($key === 'Complet') {
                $count = ($statsBrutes->get('Complet', 0) + $statsBrutes->get('termine', 0));
            } elseif ($key === 'Partiel') {
                $count = ($statsBrutes->get('Partiel', 0) + $statsBrutes->get('réponse partielle', 0));
            } else {
                $count = $statsBrutes->get($key, 0);
            }
            
            $chartLabels[] = $label;
            $chartData[] = $count;
        }

        // On met à jour le KPI des échantillons terminés.
        $echantillonsTermines = ($chartData[2] ?? 0) + ($chartData[4] ?? 0) + ($chartData[5] ?? 0); // Complet + Refus + Impossible

        // --- Performance des téléopérateurs (votre logique existante) ---
        $teleoperateurs = User::role('Téléopérateur')
            ->withCount(['echantillons as echantillons_traites' => function ($query) {
                $query->whereIn('statut', ['répondu', 'termine', 'Complet', 'refus', 'impossible de contacter']);
            }])
            ->withCount(['rendezVous as rdv_pris'])
            ->get();
            
        return view('admin.dashboard.index', compact(
            'totalTeleoperateurs',
            'totalEchantillons',
            'rendezVousAujourdhui',
            'echantillonsTermines',
            'chartLabels',
            'chartData',
            'teleoperateurs'
        ));
    }
}