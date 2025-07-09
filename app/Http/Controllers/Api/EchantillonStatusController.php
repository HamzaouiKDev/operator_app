<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EchantillonEnquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EchantillonStatusController extends Controller
{
    // Remarquez le changement ici : on demande juste l'ID, pas le modèle complet
    public function markAsComplete(Request $request, $id)
    {
        return $this->updateStatus($request, $id, 'Complet');
    }

    // Remarquez le changement ici : on demande juste l'ID, pas le modèle complet
    public function markAsPartial(Request $request, $id)
    {
        return $this->updateStatus($request, $id, 'Partiel');
    }


    private function updateStatus(Request $request, $id, string $newStatus)
    {
        $user = Auth::user();

        try {
            // ÉTAPE 1 : On récupère manuellement l'échantillon.
            // findOrFail lèvera une erreur 404 si l'ID n'existe pas, ce qui est parfait.
            $echantillon = EchantillonEnquete::findOrFail($id);

            // ÉTAPE 2 : On met à jour les champs
            $echantillon->statut = $newStatus;
            $echantillon->date_traitement = now();
            $echantillon->save(); // save() effectuera maintenant un UPDATE car le modèle a été chargé depuis la BDD.

            Log::info("[API Status Update] L'échantillon #{$echantillon->id} a été marqué comme '{$newStatus}' par l'utilisateur #{$user->id}.");

            return response()->json([
                'success' => true,
                'message' => "L'échantillon a été marqué comme '{$newStatus}' avec succès.",
                'nouveau_statut' => $newStatus
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Gérer le cas où l'ID n'est pas trouvé
            return response()->json(['success' => false, 'message' => 'Échantillon non trouvé.'], 404);

        } catch (\Exception $e) {
            // On log la véritable erreur pour le débogage
            Log::error("[API Status Update] Erreur pour échantillon #{$id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur serveur est survenue lors de la mise à jour.'
            ], 500);
        }
    }
}