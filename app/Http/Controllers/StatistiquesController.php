<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\EchantillonEnquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiquesController extends Controller
{
    /**
     * Affiche la page des statistiques pour le téléopérateur connecté.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $userId = Auth::id();

        // --- 1. Calcul des KPIs (Logique optimisée) ---

        // Statuts considérés comme une réponse pour un calcul précis
        $statutsRepondus = ['Complet', 'termine', 'Partiel', 'réponse partielle', 'refus', 'refus final', 'un rendez-vous', 'impossible de contacter', 'répondu'];

        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $userId)->count();
        $nombreEntreprisesRepondues = EchantillonEnquete::where('utilisateur_id', $userId)->whereIn('statut', $statutsRepondus)->count();
        
        // ✅ CORRECTION: Utilisation de 'utilisateur_id' au lieu de 'user_id'
        $totalRendezVous = RendezVous::where('utilisateur_id', $userId)->count();
        $rendezVousAujourdHui = RendezVous::where('utilisateur_id', $userId)->whereDate('heure_rdv', Carbon::today())->count();

        // --- 2. Tableau de distribution des statuts (Logique optimisée) ---

        // On récupère tous les comptes de statuts en une seule requête
        $statsBrutes = EchantillonEnquete::where('utilisateur_id', $userId)
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // On fusionne les statuts similaires pour un affichage clair
        $entreprisesParStatut = [
            'en attente'              => $statsBrutes->get('en attente', 0),
            'Complet'                 => $statsBrutes->get('Complet', 0) + $statsBrutes->get('termine', 0),
            'Partiel'                 => $statsBrutes->get('Partiel', 0) + $statsBrutes->get('réponse partielle', 0),
            'refus'                   => $statsBrutes->get('refus', 0) + $statsBrutes->get('refus final', 0),
            'impossible de contacter' => $statsBrutes->get('impossible de contacter', 0),
            'un rendez-vous'          => $statsBrutes->get('un rendez-vous', 0),
            'à appeler'               => $statsBrutes->get('à appeler', 0),
        ];
        
        // On retire les statuts qui ont un compte de zéro pour ne pas surcharger le tableau
        $entreprisesParStatut = array_filter($entreprisesParStatut);

        // --- 3. Données pour le tableau d'évolution (7 derniers jours) ---
        
        $evolutionLabels = [];
        $evolutionValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $evolutionLabels[] = $date->locale('fr')->translatedFormat('D d/m');
            // On compte directement dans la base de données pour chaque jour
            // ✅ CORRECTION: Utilisation de 'utilisateur_id' au lieu de 'user_id'
            $evolutionValues[] = RendezVous::where('utilisateur_id', $userId)
                ->whereDate('created_at', $date)
                ->count();
        }
        
        // On passe les données sous forme de tableaux PHP, ce qui est plus propre pour la vue
        $evolutionRendezVous = [
            'labels' => $evolutionLabels,
            'data'   => $evolutionValues,
        ];

        // --- 4. Retourner la vue avec toutes les données ---
        return view('statistiques', compact(
            'totalRendezVous', 
            'rendezVousAujourdHui', 
            'nombreEntreprisesAttribuees', 
            'nombreEntreprisesRepondues', 
            'entreprisesParStatut',
            'evolutionRendezVous'
        ));
    }
}
