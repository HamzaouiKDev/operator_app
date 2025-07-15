<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EchantillonEnquete;
use Illuminate\Support\Facades\Auth;

class EchantillonPartielController extends Controller
{
    /**
     * Affiche la liste des échantillons avec le statut "Partiel".
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Vérification de l'authentification de l'utilisateur
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté.');
        }

        $user = Auth::user();
        $searchTerm = $request->input('search_term');

        // Requête de base pour les échantillons partiels de l'utilisateur connecté
        $echantillonsQuery = EchantillonEnquete::where('utilisateur_id', $user->id)
            ->where('statut', 'Partiel');

        // Appliquer le filtre de recherche si un terme est fourni
        if ($searchTerm) {
            $echantillonsQuery->whereHas('entreprise', function ($query) use ($searchTerm) {
                $query->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
            });
        }

        // Récupération des échantillons avec pagination et chargement de la relation 'entreprise'
        $echantillons = $echantillonsQuery
            ->with('entreprise')
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->appends($request->query()); // Conserver les paramètres de recherche dans la pagination

        // Calcul des statistiques pour l'en-tête (identique aux autres pages)
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu', 'Complet'])
            ->where('utilisateur_id', $user->id)
            ->count();
            
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
            ->count();

        // Retourner la vue avec les données nécessaires
        return view('partielsRDV', compact(
            'echantillons', 
            'nombreEntreprisesRepondues', 
            'nombreEntreprisesAttribuees'
        ));
    }
}