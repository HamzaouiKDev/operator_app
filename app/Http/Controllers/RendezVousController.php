<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RendezVousController extends Controller
{
   public function index()
{
    // Vérifier si l'utilisateur est authentifié
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }

    // Récupérer l'utilisateur connecté
    $user = Auth::user();

    // Récupérer les rendez-vous de l'utilisateur avec pagination
    $rendezVous = RendezVous::where('utilisateur_id', $user->id)
        ->with('echantillonEnquete.entreprise')
        ->orderBy('echantillon_enquete_id')
        ->paginate(10);

    // Regrouper les rendez-vous par entreprise
    $rendezVousGroupedByEntreprise = $rendezVous->groupBy(function ($rdv) {
        return $rdv->echantillonEnquete->entreprise_id ?? 'sans_entreprise';
    });

    // Ajouter des statistiques ou d'autres données si nécessaire
    $nombreEntreprisesRepondues = 0; // Remplacer par la logique réelle si nécessaire
    $nombreEntreprisesAttribuees = 0; // Remplacer par la logique réelle si nécessaire

    // Retourner la vue avec la liste des rendez-vous paginés
    return view('indexRDV', compact('rendezVous', 'rendezVousGroupedByEntreprise', 'nombreEntreprisesRepondues', 'nombreEntreprisesAttribuees'));
}
    public function showByEntreprise($rendezVousId)
{
    // Vérifier si l'utilisateur est authentifié
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
    }

    // Récupérer le rendez-vous sélectionné
    $selectedRdv = RendezVous::with('echantillonEnquete.entreprise')
        ->where('utilisateur_id', Auth::user()->id)
        ->findOrFail($rendezVousId);

    // Vérifier si une entreprise est associée
    if (!$selectedRdv->echantillonEnquete || !$selectedRdv->echantillonEnquete->entreprise) {
        return redirect()->back()->with('error', 'Aucune entreprise associée à ce rendez-vous.');
    }

    $entreprise = $selectedRdv->echantillonEnquete->entreprise;
    $echantillon = $selectedRdv->echantillonEnquete;

    // Récupérer tous les rendez-vous de l'utilisateur liés à cette entreprise via les échantillons
    $rendezVous = RendezVous::where('utilisateur_id', Auth::user()->id)
        ->whereHas('echantillonEnquete', function ($query) use ($entreprise) {
            $query->where('entreprise_id', $entreprise->id);
        })
        ->with('echantillonEnquete')
        ->orderBy('echantillon_enquete_id')
        ->paginate(10);

    // Regrouper par échantillon pour l'affichage détaillé
    $rendezVousGrouped = $rendezVous->groupBy('echantillon_enquete_id');

    // Ajouter des statistiques ou d'autres données si nécessaire
    $nombreEntreprisesRepondues = 0; // Remplacer par la logique réelle si nécessaire
    $nombreEntreprisesAttribuees = 0; // Remplacer par la logique réelle si nécessaire

    // Retourner la vue index avec les données
    return view('index', compact('rendezVous', 'rendezVousGrouped', 'entreprise', 'echantillon', 'nombreEntreprisesRepondues', 'nombreEntreprisesAttribuees'));
}

    public function store(Request $request, $id)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour créer un rendez-vous.');
        }

        // Validation des données
        $request->validate([
            'heure_debut' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            // Création du rendez-vous
            $rendezVous = RendezVous::create([
                'echantillon_enquete_id' => $id,
                'utilisateur_id' => Auth::user()->id,
                'heure_debut' => $request->heure_debut,
                'heure_fin' => date('Y-m-d H:i:s', strtotime($request->heure_debut . ' +1 hour')),
                'statut' => 'planifie',
                'notes' => $request->notes,
            ]);

            // Retour avec un message de succès
            return redirect()->back()->with('success', 'Rendez-vous créé avec succès.');
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création du rendez-vous.');
        }
    }
}
