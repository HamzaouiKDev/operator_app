<?php

namespace App\Http\Controllers;

use App\Models\EchantillonEnquete;
use App\Models\Suivi; // Assurez-vous que ce chemin est correct
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException as LaravelValidationException; // Pour la validation
// use Carbon\Carbon; // Pas strictement nécessaire si on utilise que now() et created_at
// use Illuminate\Support\Facades\DB; // Pas nécessaire pour les requêtes actuelles

class SuiviController extends Controller
{
    /**
     * Affiche la liste des suivis pour l'utilisateur connecté.
     * Sert la vue 'indexSuivis.blade.php'.
     */
    public function indexSuivis(Request $request)
    {
        Log::info("[SuiviController@indexSuivis] Accès par Utilisateur ID: " . (Auth::id() ?? 'Non connecté'));
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        $searchTerm = $request->input('search_term'); // Terme de recherche générique

        $suivisQuery = Suivi::where('utilisateur_id', $user->id)
                            ->with('echantillonEnquete.entreprise'); // Charger les relations pour l'affichage

        if ($searchTerm) {
            $suivisQuery->where(function ($query) use ($searchTerm) {
                $query->where('note', 'like', '%' . $searchTerm . '%') // Recherche dans les notes
                      ->orWhere('cause_suivi', 'like', '%' . $searchTerm . '%') // Recherche dans la cause
                      ->orWhereHas('echantillonEnquete.entreprise', function ($q_entreprise) use ($searchTerm) {
                          $q_entreprise->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
                      });
            });
        }

        // Trier par date de création (qui sert de date de suivi) la plus récente en premier
        $suivis = $suivisQuery->orderBy('created_at', 'desc') // ✅ UTILISE created_at POUR LE TRI
                             ->paginate(10, ['*'], 'page_suivis')
                             ->appends($request->except('page_suivis'));

        Log::info("[SuiviController@indexSuivis] Nombre de suivis trouvés pour Utilisateur ID {$user->id} (recherche: '{$searchTerm}'): " . $suivis->total());

        // Statistiques globales
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu'])
                                            ->where('utilisateur_id', $user->id)
                                            ->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                            ->count();

        return view('indexSuivis', compact(
            'suivis',
            'nombreEntreprisesRepondues',
            'nombreEntreprisesAttribuees'
        ));
    }

    /**
     * Crée un suivi (typiquement une "relance").
     * Appelée via AJAX.
     */
    public function creerRappel(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning("[SuiviController@creerRappel] Tentative de création par utilisateur non authentifié.");
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        // Validation des données
        try {
            $validated = $request->validate([
                'echantillon_enquete_id' => 'required|exists:echantillons_enquetes,id',
                'note'                   => 'nullable|string|max:1000', // ✅ MODIFIÉ: 'commentaire' devient 'note'
                'cause_suivi'            => 'required|string|max:255',
            ]);
        } catch (LaravelValidationException $e) {
            Log::error("[SuiviController@creerRappel] Erreurs de validation: ", $e->errors());
            return response()->json(['success' => false, 'message' => 'Données invalides.', 'errors' => $e->errors()], 422);
        }
        

        // Vérification : l'échantillon est-il assigné à cet utilisateur ?
        $echantillon = EchantillonEnquete::where('id', $validated['echantillon_enquete_id'])
                                        ->where('utilisateur_id', $user->id)
                                        ->first();

        if (!$echantillon) {
            Log::warning("[SuiviController@creerRappel] Échantillon ID {$validated['echantillon_enquete_id']} non trouvé ou non assigné à l'utilisateur ID {$user->id}.");
            return response()->json(['success' => false, 'message' => 'العينة غير موجودة أو غير مخصصة لك.'], 403); // 403 Forbidden est plus approprié
        }

        try {
            $suivi = Suivi::create([
                'echantillon_enquete_id' => $validated['echantillon_enquete_id'],
                'utilisateur_id'         => $user->id,
                // 'date_suivi'          => now(), // ✅ SUPPRIMÉ: created_at sera utilisé automatiquement
                'note'                   => $validated['note'] ?? null, // ✅ MODIFIÉ: utilise 'note' et ?? null pour un vrai null si vide
                // 'resultat'            => 'relance', // ✅ SUPPRIMÉ
                'cause_suivi'            => $validated['cause_suivi'],
            ]);

            // Mettre à jour le statut de l'échantillon si nécessaire, par exemple :
            // $echantillon->statut = 'suivi planifié'; // Ou un autre statut pertinent
            // $echantillon->save();
            // Log::info("Statut de l'échantillon #{$echantillon->id} mis à jour après création du suivi.");


            Log::info("🔔 Suivi ID #{$suivi->id} (cause: {$suivi->cause_suivi}) créé pour l'échantillon #{$suivi->echantillon_enquete_id} par l'utilisateur #{$user->id}.");

            return response()->json([
                'success' => true,
                'message' => '👍 تم تسجيل المتابعة بنجاح!',
                'suivi'   => $suivi // Optionnel : retourner l'objet suivi créé
            ]);

        } catch (\Exception $e) { // Exception plus générique pour QueryException etc.
            Log::error("❌ Erreur lors de la création du suivi pour l'échantillon ID {$validated['echantillon_enquete_id']}: " . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تسجيل المتابعة.'], 500);
        }
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'echantillon_enquete_id' => 'required|exists:echantillons_enquetes,id',
        'cause_suivi' => 'required|string|max:255',
        'note' => 'nullable|string',
    ]);

    try {
        $suivi = Suivi::create([
            'echantillon_enquete_id' => $validated['echantillon_enquete_id'],
            'cause_suivi' => $validated['cause_suivi'],
            'note' => $validated['note'],
            'utilisateur_id' => Auth::id(), // Associer à l'utilisateur connecté
            'date_suivi' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ المتابعة بنجاح!',
            'suivi' => $suivi,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Erreur lors de la création du suivi: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'فشل في حفظ المتابعة. يرجى المحاولة لاحقًا.',
        ], 500);
    }
}
}