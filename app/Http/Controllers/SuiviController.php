<?php

namespace App\Http\Controllers;

use App\Models\EchantillonEnquete;
use App\Models\Suivi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable; // Important pour la gestion des erreurs

class SuiviController extends Controller
{
    /**
     * Affiche la liste des suivis pour l'utilisateur connecté.
     */
    public function indexSuivis(Request $request)
    {
        $user = Auth::user();
        $searchTerm = $request->input('search_term');

        $suivisQuery = Suivi::where('utilisateur_id', $user->id)
                            ->with('echantillonEnquete.entreprise');

        if ($searchTerm) {
            $suivisQuery->where(function ($query) use ($searchTerm) {
                $query->where('note', 'like', '%' . $searchTerm . '%')
                      ->orWhere('cause_suivi', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('echantillonEnquete.entreprise', function ($q_entreprise) use ($searchTerm) {
                          $q_entreprise->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
                      });
            });
        }

        $suivis = $suivisQuery->orderBy('created_at', 'desc')
                            ->paginate(10, ['*'], 'page_suivis')
                            ->appends($request->except('page_suivis'));

        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu'])
                                            ->where('utilisateur_id', $user->id)
                                            ->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                            ->count();

        return view('indexSuivi', compact(
            'suivis',
            'nombreEntreprisesRepondues',
            'nombreEntreprisesAttribuees'
        ));
    }

    /**
     * Enregistre un nouveau suivi (rappel) et met à jour le statut de l'échantillon parent à "à appeler".
     * C'est la méthode principale appelée par le formulaire.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        // 1. Valider les données reçues du formulaire
        $validated = $request->validate([
            'echantillon_enquete_id' => 'required|exists:echantillons_enquetes,id',
            'cause_suivi' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            // On commence une transaction pour assurer que les 2 opérations réussissent ou échouent ensemble
            DB::transaction(function () use ($validated, $user) {
                
                // On récupère l'échantillon et on le verrouille pour la mise à jour
                $echantillon = EchantillonEnquete::where('id', $validated['echantillon_enquete_id'])
                                                ->where('utilisateur_id', $user->id)
                                                ->lockForUpdate() // Verrouille la ligne pour éviter les conflits
                                                ->firstOrFail(); // Renvoie une erreur si non trouvé ou non assigné

                // ACTION N°1 : Créer l'enregistrement dans la table 'suivis'
                Suivi::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id'       => $user->id,
                    'cause_suivi'          => $validated['cause_suivi'],
                    'note'                 => $validated['note'] ?? null,
                ]);

                // ACTION N°2 : Mettre à jour le statut de l'échantillon parent
                $echantillon->statut = 'à appeler'; // <-- C'EST LA NOUVELLE LOGIQUE
                $echantillon->save(); // On sauvegarde la modification

                Log::info("✅ Suivi créé pour l'échantillon #{$echantillon->id}. Statut mis à jour à 'à appeler' par l'utilisateur #{$user->id}.");

            }); // La transaction est validée ici si aucune erreur n'est survenue

            // 4. Renvoyer une réponse de succès au JavaScript
            return response()->json([
                'success' => true,
                'message' => 'Suivi ajouté et statut mis à jour avec succès !',
                'nouveau_statut' => 'à appeler'
            ]);

        } catch (Throwable $e) {
            // En cas d'erreur, la transaction est automatiquement annulée
            Log::error("❌ Erreur lors de la création du suivi pour l'échantillon ID {$validated['echantillon_enquete_id']}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Une erreur serveur est survenue lors de l\'enregistrement.'], 500);
        }
    }

    // La méthode creerRappel() devient redondante et peut être supprimée pour éviter la confusion.
    // La méthode store() est maintenant la seule méthode pour créer un suivi.
}