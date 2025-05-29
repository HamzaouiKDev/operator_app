<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suivi; // ✅ Assurez-vous que ce chemin est correct
use App\Models\EchantillonEnquete; // ✅ Assurez-vous que ce chemin est correct
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
// Si vous avez déjà une méthode 'store', vous pouvez ajouter 'creerRappel' à la suite.

class SuiviController extends Controller
{
    // Votre méthode store existante (si elle existe)
    // public function store(Request $request)
    // {
    //     // Votre logique existante pour la route relance.store
    // }

    /**
     * Crée un suivi de type "rappel" ou "relance" pour un échantillon.
     */
    public function creerRappel(Request $request) // ✅ C'EST LA MÉTHODE À AJOUTER/VÉRIFIER
    {
        $user = Auth::user();

        // L'utilisateur doit être authentifié (normalement géré par le middleware de la route)
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        $validated = $request->validate([
    'echantillon_enquete_id' => 'required|exists:echantillons_enquetes,id',
    'commentaire'             => 'nullable|string|max:1000',
    'cause_suivi'             => 'required|string|max:255', // Ajoutez la validation pour cause_suivi
]);

        // Vérification optionnelle : l'échantillon est-il assigné à cet utilisateur ?
        $echantillon = EchantillonEnquete::where('id', $validated['echantillon_enquete_id'])
                                        ->where('utilisateur_id', $user->id)
                                        ->first();

        if (!$echantillon) {
            return response()->json(['success' => false, 'message' => 'العينة غير موجودة أو غير مخصصة لك.'], 404);
        }

        try {
           $suivi = Suivi::create([
    'echantillon_enquete_id' => $validated['echantillon_enquete_id'],
    'utilisateur_id'          => $user->id,
    'date_suivi'              => now(),
    'commentaire'             => $validated['commentaire'] ?? 'متابعة مطلوبة',
    'resultat'                => 'relance',
    'cause_suivi'             => $validated['cause_suivi'], // Ajoutez cause_suivi ici
]);

            Log::info("🔔 متابعة/تذكير مسجل للعينة #{$suivi->echantillon_enquete_id} بواسطة المستخدم #{$user->id}. رقم المتابعة: #{$suivi->id}");

            return response()->json([
                'success' => true,
                'message' => '👍 تم تسجيل المتابعة بنجاح!', // Message de succès en arabe
                'suivi'   => $suivi // Optionnel : retourner l'objet suivi créé
            ]);

        } catch (\Exception $e) {
            Log::error("❌ خطأ أثناء تسجيل المتابعة/التذكير: " . $e->getMessage() . "\n" . $e->getTraceAsString()); // Ajout du stack trace pour plus de détails
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تسجيل المتابعة.'], 500);
        }
    }
}