<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EchantillonEnquete;
use Illuminate\Support\Facades\Auth;

class EchantillonController extends Controller
{
    /**
     * Affiche la liste des échantillons et attribue un échantillon non attribué à l'utilisateur connecté.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Vérifier si l'utilisateur est bien authentifié
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Attribuer automatiquement le premier échantillon non attribué si l'utilisateur n'en a pas déjà un
        $echantillon = EchantillonEnquete::where('utilisateur_id', $user->id)
            ->with(['entreprise', 'entreprise.telephones', 'entreprise.contacts'])
            ->first();

        if (!$echantillon) {
            $echantillon = EchantillonEnquete::whereNull('utilisateur_id')
                ->orderBy('priorite', 'asc') // Prioriser selon le champ priorite
                ->with(['entreprise', 'entreprise.telephones', 'entreprise.contacts'])
                ->first();

            // Si un échantillon est trouvé, l'attribuer à l'utilisateur
            if ($echantillon) {
                $echantillon->update(['utilisateur_id' => $user->id]);
            }
        }

        // Calculer le nombre d'entreprises ayant répondu pour l'utilisateur en cours
        $nombreEntreprisesRepondues = EchantillonEnquete::where('statut', 'termine')
            ->where('utilisateur_id', $user->id)
            ->count();

        // Calculer le nombre total d'entreprises attribuées à l'utilisateur en cours
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
            ->count();

        // Retourner la vue avec l'échantillon et les compteurs
        return view('index', compact('echantillon', 'nombreEntreprisesRepondues', 'nombreEntreprisesAttribuees'));
    }

    /**
     * Met à jour le statut d'un échantillon spécifique.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatut(Request $request, $id)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Vérifier si l'utilisateur est authentifié
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        // Récupérer l'échantillon attribué à l'utilisateur
        $echantillon = EchantillonEnquete::where('id', $id)
            ->where('utilisateur_id', $user->id)
            ->first();

        // Vérifier si l'échantillon existe et appartient à l'utilisateur
        if (!$echantillon) {
            return response()->json(['success' => false, 'message' => 'Échantillon non trouvé ou non attribué à cet utilisateur.'], 404);
        }

        // Valider le statut reçu
        $statut = $request->input('statut');
        $statutsAutorises = ['répondu', 'réponse partielle', 'un rendez-vous', 'pas de réponse', 'refus', 'introuvable'];

        if (!in_array($statut, $statutsAutorises)) {
            return response()->json(['success' => false, 'message' => 'Statut invalide.'], 400);
        }

        // Mettre à jour le statut de l'échantillon
        $echantillon->update(['statut' => $statut]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'statut' => $statut
        ]);
    }

    /**
     * Passe à l'échantillon suivant pour l'utilisateur connecté.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function next(Request $request)
    {
        // Vérifier si un utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour passer à l\'échantillon suivant.');
        }

        $userId = Auth::id();

        // Récupérer l'échantillon actuel attribué à l'utilisateur (si existant)
        $echantillonActuel = EchantillonEnquete::where('utilisateur_id', $userId)
            ->first();

        // Si un échantillon actuel existe, le libérer (réinitialiser utilisateur_id à null)
        if ($echantillonActuel) {
            $echantillonActuel->update(['utilisateur_id' => null]);
        }

        // Chercher un nouvel échantillon non attribué
        $nouvelEchantillon = EchantillonEnquete::whereNull('utilisateur_id')
            ->orderBy('priorite', 'asc') // Prioriser selon le champ priorite
            ->with(['entreprise', 'entreprise.telephones', 'entreprise.contacts'])
            ->first();

        // Si un nouvel échantillon est trouvé, l'attribuer à l'utilisateur
        if ($nouvelEchantillon) {
            $nouvelEchantillon->update(['utilisateur_id' => $userId]);
            return redirect()->route('echantillons.index')->with('success', 'Nouvel échantillon attribué avec succès.');
        }

        // Si aucun échantillon n'est disponible, rediriger avec un message d'erreur
        return redirect()->route('echantillons.index')->with('error', 'Aucun nouvel échantillon disponible pour le moment.');
    }
}
