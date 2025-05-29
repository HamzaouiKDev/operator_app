<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\EchantillonEnquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatistiquesController extends Controller
{

    /*public function index()
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Statistiques sur les rendez-vous
        $totalRendezVous = RendezVous::where('utilisateur_id', $user->id)->count();
        $rendezVousAujourdHui = RendezVous::where('utilisateur_id', $user->id)
            ->whereDate('heure_debut', Carbon::today())
            ->count();

        // Répartition des rendez-vous par statut
        $rendezVousParStatut = [
            'planifie' => RendezVous::where('utilisateur_id', $user->id)->where('statut', 'planifie')->count(),
            'confirme' => RendezVous::where('utilisateur_id', $user->id)->where('statut', 'confirme')->count(),
            'annule' => RendezVous::where('utilisateur_id', $user->id)->where('statut', 'annule')->count(),
            'termine' => RendezVous::where('utilisateur_id', $user->id)->where('statut', 'termine')->count(),
        ];

        // Statistiques sur les entreprises
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)->distinct('entreprise_id')->count('entreprise_id');
        $nombreEntreprisesRepondues = EchantillonEnquete::where('utilisateur_id', $user->id)->where('statut', 'répondu')->distinct('entreprise_id')->count('entreprise_id');

        // Répartition des entreprises par statut
        $entreprisesParStatut = [
            'repondu' => EchantillonEnquete::where('utilisateur_id', $user->id)->where('statut', 'répondu')->distinct('entreprise_id')->count('entreprise_id'),
            'partiel' => EchantillonEnquete::where('utilisateur_id', $user->id)->where('statut', 'réponse partielle')->distinct('entreprise_id')->count('entreprise_id'),
            'pas_reponse' => EchantillonEnquete::where('utilisateur_id', $user->id)->where('statut', 'pas de réponse')->distinct('entreprise_id')->count('entreprise_id'),
            'refus' => EchantillonEnquete::where('utilisateur_id', $user->id)->where('statut', 'refus')->distinct('entreprise_id')->count('entreprise_id'),
        ];

        // Évolution des rendez-vous sur les 7 derniers jours
        $evolutionRendezVous = [
            'labels' => [],
            'data' => []
        ];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $evolutionRendezVous['labels'][] = '"' . $date->format('d/m') . '"';
            $evolutionRendezVous['data'][] = RendezVous::where('utilisateur_id', $user->id)
                ->whereDate('heure_debut', $date)
                ->count();
        }
        $evolutionRendezVous['labels'] = implode(', ', $evolutionRendezVous['labels']);
        $evolutionRendezVous['data'] = implode(', ', $evolutionRendezVous['data']);

        // Retourner la vue avec les données statistiques
        return view('statistiques', compact(
            'totalRendezVous', 
            'rendezVousAujourdHui', 
            'nombreEntreprisesAttribuees', 
            'nombreEntreprisesRepondues', 
            'rendezVousParStatut', 
            'entreprisesParStatut', 
            'evolutionRendezVous'
        ));
    }*/
    public function index()
{
    // Vérifier si l'utilisateur est authentifié
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }

    // Statistiques de base
    $totalRendezVous = 10; // Valeur statique pour tester
    $rendezVousAujourdHui = 2; // Valeur statique pour tester
    $nombreEntreprisesAttribuees = 5; // Valeur statique pour tester
    $nombreEntreprisesRepondues = 3; // Valeur statique pour tester

    // Répartition des rendez-vous par statut
    $rendezVousParStatut = [
        'planifie' => 5,
        'confirme' => 3,
        'annule' => 2,
        'termine' => 0,
    ];

    // Répartition des entreprises par statut
    $entreprisesParStatut = [
        'repondu' => 2,
        'partiel' => 1,
        'pas_reponse' => 1,
        'refus' => 1,
    ];

    // Évolution des rendez-vous sur les 7 derniers jours
    $evolutionRendezVous = [
        'labels' => '"يوم 1", "يوم 2", "يوم 3", "يوم 4", "يوم 5", "يوم 6", "يوم 7"',
        'data' => '1, 2, 0, 3, 1, 2, 1',
    ];

    // Retourner la vue avec les données statistiques
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
