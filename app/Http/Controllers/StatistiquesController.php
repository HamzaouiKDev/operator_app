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

        // --- Calcul des statistiques pour les cartes (INCHANGÉ) ---

        $totalRendezVous = RendezVous::where('utilisateur_id', $userId)->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $userId)->distinct('entreprise_id')->count('entreprise_id');
        $nombreEntreprisesRepondues = EchantillonEnquete::where('utilisateur_id', $userId)->whereIn('statut', ['répondu', 'Complet', 'termine'])->distinct('entreprise_id')->count('entreprise_id');
        
        $tousLesRdv = RendezVous::where('utilisateur_id', $userId)->pluck('heure_rdv');
        $rendezVousAujourdHui = 0;
        foreach ($tousLesRdv as $dateStr) {
            try {
                if (Carbon::parse($dateStr)->isToday()) {
                    $rendezVousAujourdHui++;
                }
            } catch (\Exception $e) { continue; }
        }

        // --- NOUVELLE LOGIQUE OPTIMISÉE POUR COMPTER TOUS LES STATUTS ---

        // 1. On récupère tous les statuts et leur compte pour l'utilisateur en une seule requête.
        $statsBrutes = EchantillonEnquete::where('utilisateur_id', $userId)
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // 2. On définit la liste complète des statuts à afficher.
        $entreprisesParStatut = [
            'en attente'              => $statsBrutes->get('en attente', 0),
            'Complet'                 => $statsBrutes->get('Complet', 0) + $statsBrutes->get('termine', 0),
            'Partiel'                 => $statsBrutes->get('Partiel', 0) + $statsBrutes->get('réponse partielle', 0),
            'refus'                   => $statsBrutes->get('refus', 0),
            'impossible de contacter' => $statsBrutes->get('impossible de contacter', 0),
            'un rendez-vous'          => $statsBrutes->get('un rendez-vous', 0),
            'à appeler'               => $statsBrutes->get('à appeler', 0),
            
        ];

        // --- Calcul des autres statistiques (INCHANGÉ) ---

        $rendezVousParStatutRaw = RendezVous::where('utilisateur_id', $userId)
            ->select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $rendezVousParStatut = [
            'en attente'              => $statsBrutes->get('en attente', 0),
            'Complet'                 => $statsBrutes->get('Complet', 0) + $statsBrutes->get('termine', 0), // Fusionne Complet et termine
            'Partiel'                 => $statsBrutes->get('Partiel', 0) + $statsBrutes->get('réponse partielle', 0), // Fusionne Partiel et réponse partielle
            'refus'                   => $statsBrutes->get('refus', 0),
            'impossible de contacter' => $statsBrutes->get('impossible de contacter', 0),
            'un rendez-vous'          => $statsBrutes->get('un rendez-vous', 0),
            'à appeler'               => $statsBrutes->get('à appeler', 0),
            
        ];

        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $endDate = Carbon::today()->endOfDay();
        $evolutionData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $evolutionData[$date->format('Y-m-d')] = 0;
        }
        foreach ($tousLesRdv as $dateStr) {
            try {
                $date = Carbon::parse($dateStr);
                if ($date->between($startDate, $endDate)) {
                    $evolutionData[$date->format('Y-m-d')]++;
                }
            } catch (\Exception $e) { continue; }
        }
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

        // --- Retourner la vue avec toutes les données ---
        
        return view('statistiques', compact(
            'totalRendezVous', 
            'rendezVousAujourdHui', 
            'nombreEntreprisesAttribuees', 
            'nombreEntreprisesRepondues', 
            'rendezVousParStatut', 
            'entreprisesParStatut', // La nouvelle variable avec tous les comptes
            'evolutionRendezVous'
        ));
    }
}