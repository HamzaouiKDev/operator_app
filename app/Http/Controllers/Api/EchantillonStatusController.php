<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EchantillonEnquete; // Importez votre modèle
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Pour retourner des réponses JSON

class EchantillonStatusController extends Controller
{
    /**
     * Marque un échantillon d'enquête comme 'Complet'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id L'ID de l'échantillon d'enquête à mettre à jour.
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsComplete(Request $request, $id): JsonResponse
    {
        // Trouver l'échantillon par son ID
        $echantillon = EchantillonEnquete::find($id);

        // Vérifier si l'échantillon existe
        if (!$echantillon) {
            return response()->json(['message' => 'Échantillon non trouvé.'], 404);
        }

        // Mettre à jour le statut
        $echantillon->statut = 'Complet';
        $echantillon->save();

        // Retourner une réponse JSON de succès
        return response()->json(['message' => 'Statut de l\'échantillon mis à jour à Complet avec succès.'], 200);
    }
}