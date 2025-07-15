<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\EchantillonEnquete;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SupervisorController extends Controller
{
    /**
     * Affiche le tableau de bord du superviseur.
     * (Version restaurée comme demandé)
     */
    public function index(Request $request)
    {
        // --- 1. Définitions ---
        $statutsComplets = ['Complet', 'termine'];
        $statutsPartiels = ['Partiel', 'réponse partielle'];
        $statutsRefus = ['refus', 'refus final'];
        $statutRendezVous = 'un rendez-vous';
        $statutsImpossible = ['impossible de contacter'];
        $statutsAAppeler = ['à appeler'];
        $historyTableName = 'echantillon_statut_histories';
        $historyStatusColumnName = 'nouveau_statut';

        // --- 2. Initialisation et Filtres ---
        $viewData = [
            'teleoperateurs' => User::role('Téléopérateur')->orderBy('name')->get(),
            'selectedTeleoperateur' => null,
            'date' => Carbon::now()->format('d/m/Y'),
        ];
        $teleoperateurId = $request->input('teleoperateur_id');

        $baseQuery = EchantillonEnquete::query();
        $historyQuery = DB::table($historyTableName);

        if ($teleoperateurId) {
            $selectedTeleoperateur = User::findOrFail($teleoperateurId);
            if (!$selectedTeleoperateur->hasRole('Téléopérateur')) {
                abort(403, 'L\'utilisateur sélectionné n\'est pas un téléopérateur.');
            }
            $viewData['selectedTeleoperateur'] = $selectedTeleoperateur;
            $baseQuery->where('utilisateur_id', $teleoperateurId);
            $historyQuery->where('user_id', $teleoperateurId);
        }

        // --- 3. Calcul des KPIs ---
        $nombreEchantillonsComplets = $baseQuery->clone()->whereIn('statut', $statutsComplets)->count();
        $nombreEchantillonsRefus = $baseQuery->clone()->whereIn('statut', $statutsRefus)->count();
        $nombreEchantillonsPartiels = $baseQuery->clone()->whereIn('statut', $statutsPartiels)->count();
        $nombreEchantillonsSuivi = $baseQuery->clone()->whereIn('statut', $statutsAAppeler)->count();
        $nombreEchantillonsImpossible = $baseQuery->clone()->whereIn('statut', $statutsImpossible)->count();
        
        $echantillonsAvecPartielIds = $historyQuery->clone()->whereIn($historyStatusColumnName, $statutsPartiels)->distinct()->pluck('echantillon_enquete_id');
        $queryRdv = $baseQuery->clone()->where('statut', $statutRendezVous);
        $nombreRdvAvecPartiel = $queryRdv->clone()->whereIn('id', $echantillonsAvecPartielIds)->count();
        $nombreRdvSansPartiel = $queryRdv->clone()->whereNotIn('id', $echantillonsAvecPartielIds)->count();
        $totalEchantillons = $teleoperateurId ? $baseQuery->clone()->count() : EchantillonEnquete::count();

        // Création du tableau $statsGlobales pour la vue du tableau de bord
        $statsGlobales = [
            'total' => $totalEchantillons,
            'complets' => $nombreEchantillonsComplets,
            'partiels' => $nombreEchantillonsPartiels,
            'refus' => $nombreEchantillonsRefus,
            'suivis' => $nombreEchantillonsSuivi,
            'impossible' => $nombreEchantillonsImpossible,
            'rdv_avec_partiel' => $nombreRdvAvecPartiel,
            'rdv_sans_partiel' => $nombreRdvSansPartiel,
        ];
        $traiteGlobal = $statsGlobales['complets'] + $statsGlobales['partiels'] + $statsGlobales['refus'] + $statsGlobales['suivis'] + $statsGlobales['impossible'] + $statsGlobales['rdv_avec_partiel'] + $statsGlobales['rdv_sans_partiel'];
        $statsGlobales['en_attente'] = max(0, $totalEchantillons - $traiteGlobal);
        
        // Ajout de toutes les variables à $viewData pour les rendre accessibles dans la vue.
        $viewData['statsGlobales'] = $statsGlobales;
        $viewData['nombreEchantillonsComplets'] = $nombreEchantillonsComplets;
        $viewData['nombreEchantillonsPartiels'] = $nombreEchantillonsPartiels;
        $viewData['nombreRdvAvecPartiel'] = $nombreRdvAvecPartiel;
        $viewData['nombreRdvSansPartiel'] = $nombreRdvSansPartiel;
        $viewData['nombreEchantillonsSuivi'] = $nombreEchantillonsSuivi;
        $viewData['nombreEchantillonsRefus'] = $nombreEchantillonsRefus;
        $viewData['nombreEchantillonsImpossible'] = $nombreEchantillonsImpossible;
        $viewData['totalEchantillons'] = $totalEchantillons;


        // --- 4. Préparation des données pour les graphiques ---
        if (!$teleoperateurId) {
            if ($statsGlobales['total'] > 0) {
                $counts = [
                    'Complets' => $statsGlobales['complets'],
                    'Partiels' => $statsGlobales['partiels'],
                    'RDV (avec partiel)' => $statsGlobales['rdv_avec_partiel'],
                    'RDV (sans partiel)' => $statsGlobales['rdv_sans_partiel'],
                    'À rappeler' => $statsGlobales['suivis'],
                    'Refus' => $statsGlobales['refus'],
                    'Contact impossible' => $statsGlobales['impossible'],
                    'En attente' => $statsGlobales['en_attente'],
                ];
                
                $viewData['statutsChartData'] = [
                    'labels' => array_keys($counts),
                    'data' => array_values(array_filter($counts, fn($c) => $c >= 0)),
                    'colors' => ['#10b981', '#f59e0b', '#14b8a6', '#60a5fa', '#8b5cf6', '#ef4444', '#6b7280', '#9ca3af']
                ];
            }
        }

        return view('supervisor.dashboard', $viewData);
    }

    /**
     * Génère un rapport de performance en PDF en français.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generatePdfReport(Request $request)
    {
        try {
            // --- 1. Définitions des statuts ---
            $statuts = [
                'complets'   => ['Complet', 'termine'],
                'partiels'   => ['Partiel', 'réponse partielle'],
                'refus'      => ['refus', 'refus final'],
                'suivis'     => ['à appeler'],
                'impossible' => ['impossible de contacter'],
            ];
            $statutRendezVous = 'un rendez-vous';
            $historyTableName = 'echantillon_statut_histories';
            $historyStatusColumnName = 'nouveau_statut';

            // --- 2. Calcul des statistiques globales ---
            $statsGlobales = [];
            foreach ($statuts as $key => $value) {
                $statsGlobales[$key] = EchantillonEnquete::whereIn('statut', $value)->count();
            }
            // Calcul des RDV globaux
            $historyQueryGlobal = DB::table($historyTableName);
            $echantillonsAvecPartielIdsGlobal = $historyQueryGlobal->whereIn($historyStatusColumnName, $statuts['partiels'])->distinct()->pluck('echantillon_enquete_id');
            $queryRdvGlobal = EchantillonEnquete::where('statut', $statutRendezVous);
            $statsGlobales['rdv_avec_partiel'] = $queryRdvGlobal->clone()->whereIn('id', $echantillonsAvecPartielIdsGlobal)->count();
            $statsGlobales['rdv_sans_partiel'] = $queryRdvGlobal->clone()->whereNotIn('id', $echantillonsAvecPartielIdsGlobal)->count();
            $statsGlobales['total'] = EchantillonEnquete::count();

            // --- 3. Calcul des statistiques par opérateur ---
            $statsOperateurs = [];
            $teleoperateurs = User::role('Téléopérateur')->get();

            foreach ($teleoperateurs as $operateur) {
                $opStats = [];
                $baseQuery = EchantillonEnquete::where('utilisateur_id', $operateur->id);
                
                foreach ($statuts as $key => $value) {
                    $opStats[$key] = $baseQuery->clone()->whereIn('statut', $value)->count();
                }

                // Calcul des RDV par opérateur
                $historyQueryOp = DB::table($historyTableName)->where('user_id', $operateur->id);
                $echantillonsAvecPartielIdsOp = $historyQueryOp->whereIn($historyStatusColumnName, $statuts['partiels'])->distinct()->pluck('echantillon_enquete_id');
                $queryRdvOp = $baseQuery->clone()->where('statut', $statutRendezVous);
                $opStats['rdv_avec_partiel'] = $queryRdvOp->clone()->whereIn('id', $echantillonsAvecPartielIdsOp)->count();
                $opStats['rdv_sans_partiel'] = $queryRdvOp->clone()->whereNotIn('id', $echantillonsAvecPartielIdsOp)->count();
                
                $opStats['total'] = $baseQuery->clone()->count();

                // Ajoute les stats de cet opérateur à la liste s'il a des échantillons
                if ($opStats['total'] > 0) {
                    $statsOperateurs[] = array_merge(['nom' => $operateur->name], $opStats);
                }
            }

            // --- 4. Préparation des données pour la vue ---
            $data = [
                'date' => Carbon::now()->format('d/m/Y'),
                'statsOperateurs' => $statsOperateurs,
                'statsGlobales' => $statsGlobales,
            ];

            // --- 5. Configuration et Génération du PDF ---
            $options = ['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'defaultFont' => 'DejaVu Sans'];
            $pdf = Pdf::setOptions($options)->loadView('reports.supervisor-report', $data);
            
            return $pdf->download('rapport-performance-' . Carbon::now()->format('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du PDF : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur inattendue est survenue lors de la création du rapport.');
        }
    }
}
