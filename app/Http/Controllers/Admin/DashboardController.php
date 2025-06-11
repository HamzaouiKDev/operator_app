<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EchantillonEnquete;
use App\Models\RendezVous;
use Spatie\Permission\Models\Role;
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
        $echantillonsTermines = EchantillonEnquete::whereIn('statut', ['répondu', 'termine', 'refus'])->count();

        // --- Données pour le graphique de progression ---
        $statutsCounts = EchantillonEnquete::select('statut', DB::raw('count(*) as total'))
                                        ->groupBy('statut')
                                        ->pluck('total', 'statut');

        $chartLabels = $statutsCounts->keys()->map(function ($statut) {
            switch ($statut) {
                case 'répondu': return 'Répondu';
                case 'termine': return 'Terminé';
                case 'refus': return 'Refus';
                case 'pas de réponse': return 'Pas de réponse';
                case 'un rendez-vous': return 'Rendez-vous';
                default: return ucfirst($statut);
            }
        });
        $chartData = $statutsCounts->values();

        // --- Performance des téléopérateurs ---
        $teleoperateurs = User::role('Téléopérateur')
            // 👇 CHANGEMENT ICI : Utilise la méthode echantillons() de votre modèle User
            ->withCount(['echantillons as echantillons_traites' => function ($query) {
                $query->whereIn('statut', ['répondu', 'termine', 'refus']);
            }])
            // 👇 CHANGEMENT ICI : Utilise la méthode rendezVous() de votre modèle User
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