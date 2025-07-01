<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\EchantillonEnquete; // Assurez-vous que le nom du modèle est correct
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiquesController extends Controller
{
    /**
     * Affiche la page des statistiques pour le téléopérateur connecté.
     * Cette méthode calcule et transmet toutes les données nécessaires à la vue.
     */
    public function index()
    {
        // 1. Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $userId = Auth::id();

        // --- 2. Calcul des statistiques pour les cartes ---

        $totalRendezVous = RendezVous::where('utilisateur_id', $userId)->count();

        // CORRECTION DÉFINITIVE : Traitement des dates en PHP pour éviter les erreurs SQL.
        // On récupère toutes les dates textuelles pour l'utilisateur.
        $tousLesRdv = RendezVous::where('utilisateur_id', $userId)->pluck('heure_rdv');
        
        $rendezVousAujourdHui = 0;
        foreach ($tousLesRdv as $dateStr) {
            try {
                // On essaie de convertir chaque chaîne. Si ça échoue, on l'ignore.
                $date = Carbon::parse($dateStr);
                if ($date->isToday()) {
                    $rendezVousAujourdHui++;
                }
            } catch (\Exception $e) {
                // Ignore les dates invalides
                continue;
            }
        }

        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $userId)
            ->distinct('entreprise_id')
            ->count('entreprise_id');
        
        $nombreEntreprisesRepondues = EchantillonEnquete::where('utilisateur_id', $userId)
            ->where('statut', 'répondu')
            ->distinct('entreprise_id')
            ->count('entreprise_id');


        // --- 3. Calcul optimisé des répartitions par statut ---

        $rendezVousParStatutRaw = RendezVous::where('utilisateur_id', $userId)
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $rendezVousParStatut = [
            'planifie' => $rendezVousParStatutRaw['planifie'] ?? 0,
            'confirme' => $rendezVousParStatutRaw['confirme'] ?? 0,
            'annule'   => $rendezVousParStatutRaw['annule'] ?? 0,
            'termine'  => $rendezVousParStatutRaw['termine'] ?? 0,
        ];

        $entreprisesParStatutRaw = EchantillonEnquete::select('statut', DB::raw('COUNT(DISTINCT entreprise_id) as total'))
            ->where('utilisateur_id', $userId)
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $entreprisesParStatut = [
            'repondu'       => $entreprisesParStatutRaw['répondu'] ?? 0,
            'partiel'       => $entreprisesParStatutRaw['réponse partielle'] ?? 0,
            'pas_reponse'   => $entreprisesParStatutRaw['pas de réponse'] ?? 0,
            'refus'         => $entreprisesParStatutRaw['refus'] ?? 0,
        ];


        // --- 4. Calcul de l'évolution des RDV en PHP ---
        
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $endDate = Carbon::today()->endOfDay();
        
        // Initialiser un tableau pour les 7 derniers jours avec des compteurs à 0.
        $evolutionData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $evolutionData[$date->format('Y-m-d')] = 0; // Clé au format AAAA-MM-JJ
        }

        // On utilise la même liste de RDV que celle récupérée plus haut.
        foreach ($tousLesRdv as $dateStr) {
            try {
                $date = Carbon::parse($dateStr);
                // On vérifie si la date se situe dans notre intervalle de 7 jours.
                if ($date->between($startDate, $endDate)) {
                    // On incrémente le compteur pour le jour correspondant.
                    $evolutionData[$date->format('Y-m-d')]++;
                }
            } catch (\Exception $e) {
                // Ignore les dates invalides
                continue;
            }
        }

        // Formater les labels et les données pour la vue.
        $evolutionLabels = [];
        $evolutionValues = [];
        foreach ($evolutionData as $dateKey => $count) {
            $evolutionLabels[] = '"' . Carbon::parse($dateKey)->format('d/m') . '"';
            $evolutionValues[] = $count;
        }
        
        $evolutionRendezVous = [
            'labels' => implode(', ', $evolutionLabels),
            'data'   => implode(', ', $evolutionValues),
        ];

        // --- 5. Retourner la vue avec toutes les données calculées ---
        
        return view('statistiques', compact(
            'totalRendezVous', 
            'rendezVousAujourdHui', 
            'nombreEntreprisesAttribuees', 
            'nombreEntreprisesRepondues', 
            'rendezVousParStatut', 
            'entreprisesParStatut', 
            'evolutionRendezVous'
        ));
    }
}
