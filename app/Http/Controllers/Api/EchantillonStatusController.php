<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EchantillonEnquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EchantillonStatusController extends Controller
{
    public function markAsComplete(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:echantillons_enquetes,id',
        ], [
            'id.required' => 'L\'ID de l\'échantillon est requis.',
            'id.integer' => 'L\'ID de l\'échantillon doit être un entier.',
            'id.exists' => 'L\'échantillon d\'enquête avec cet ID n\'existe pas.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation des paramètres d\'URL.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $echantillon = EchantillonEnquete::find($id);

            if (!$echantillon) {
                return response()->json(['message' => 'Échantillon d\'enquête non trouvé.'], 404);
            }

            if ($echantillon->statut === 'Complet') {
                return response()->json([
                    'message' => 'L\'échantillon est déjà marqué comme Complet.',
                    'echantillon_id' => $echantillon->id
                ], 200);
            }

            $oldStatut = $echantillon->statut;
            $echantillon->statut = 'Complet';
            $echantillon->date_traitement = Carbon::now();
            $changedById = Auth::id();

            $echantillon->save();

            Log::info("API: Échantillon ID {$id} mis à jour au statut 'Complet' par utilisateur ID {$changedById}. Ancien statut: '{$oldStatut}'.");

            return response()->json([
                'message' => 'Statut de l\'échantillon mis à jour à Complet avec succès.',
                'echantillon' => $echantillon->fresh(),
                'updated_by_user_id' => $changedById
            ], 200);

        } catch (\Exception $e) {
            Log::error("API Erreur critique lors de la mise à jour du statut de l'échantillon ID {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Une erreur interne est survenue lors de la mise à jour du statut.'], 500);
        }
    }
}