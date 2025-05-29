<?php

namespace App\Http\Controllers;

use App\Models\TelephoneEntreprise;
use App\Models\Entreprise;         // ✅ Il est bon d'avoir cet import car 'entreprises' est utilisé dans les règles de validation 'exists'
use App\Models\ContactEntreprise; // ✅ De même pour 'contact_entreprises'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TelephoneController extends Controller
{
    /**
     * Enregistre un nouveau numéro de téléphone pour une entreprise.
     */
    public function store(Request $request, $entreprise_id)
    {
        // S'assurer que $entreprise_id correspond à une entreprise existante peut être une bonne validation supplémentaire
        // if (!Entreprise::find($entreprise_id)) {
        //     return redirect()->back()->with('error', 'Entreprise non trouvée.');
        // }

        $request->validate([
            'numero' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'est_primaire' => 'nullable|boolean',
            // Si ce formulaire peut aussi soumettre un contact_id :
            // 'contact_id' => 'nullable|exists:contact_entreprises,id,entreprise_id,'.$entreprise_id // S'assurer que le contact appartient à l'entreprise
        ]);

        TelephoneEntreprise::create([
            'entreprise_id' => $entreprise_id,
            // 'contact_id' => $request->input('contact_id'), // Si vous gérez le contact_id via ce formulaire
            'numero' => $request->numero,
            'source' => $request->source,
            'est_primaire' => $request->boolean('est_primaire'),
            'etat_verification' => 'non_verifie', // Statut par défaut, c'est bien
            'derniere_verification_at' => null,  // Initialisation à null, c'est bien
        ]);

        return redirect()->back()->with('success', 'Numéro de téléphone ajouté avec succès.');
    }

    /**
     * Met à jour le statut de vérification d'un numéro de téléphone existant.
     */
    public function updateStatus(Request $request, TelephoneEntreprise $telephone) // Route Model Binding
    {
        $validator = Validator::make($request->all(), [
            'statut_numero' => 'required|string|in:valide,faux_numero,pas_programme,ne_pas_deranger,non_verifie',
        ]);

        if ($validator->fails()) {
            Log::warning("[TelephoneController@updateStatus] Validation échouée pour téléphone #{$telephone->id}: ", $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Données invalides.', 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        try {
            $telephone->etat_verification = $validated['statut_numero'];
            $telephone->derniere_verification_at = now();
            $telephone->save();

            Log::info("[TelephoneController@updateStatus] Statut du téléphone #{$telephone->id} ('{$telephone->numero}') mis à jour à '{$validated['statut_numero']}'");
            return response()->json(['success' => true, 'message' => 'تم تحديث حالة الرقم بنجاح.']);

        } catch (\Exception $e) {
            Log::error("[TelephoneController@updateStatus] Erreur MAJ statut téléphone #{$telephone->id}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تحديث حالة الرقم.'], 500);
        }
    }

    /**
     * Récupère ou crée un enregistrement TelephoneEntreprise pour le numéro d'un contact spécifique.
     */
    public function getOrCreateForContact(Request $request)
    {
        Log::debug("[getOrCreateForContact] Requête reçue: ", $request->all());

        $validator = Validator::make($request->all(), [
            'entreprise_id' => 'required|exists:entreprises,id',
            'contact_id' => 'required|exists:contact_entreprises,id', // 'contact_entreprises' est bien le nom de votre table
            'numero' => 'required|string|max:255',
            // 'source' => 'nullable|string|max:255' // La source est définie par défaut, mais pourrait être passée
        ]);

        if ($validator->fails()) {
            Log::warning("[getOrCreateForContact] Validation échouée: ", $validator->errors()->toArray());
            return response()->json(['success' => false, 'message' => 'Données invalides pour la préparation du numéro de contact.', 'errors' => $validator->errors()], 422);
        }

        $entrepriseId = $request->input('entreprise_id');
        $contactId = $request->input('contact_id');
        $numero = $request->input('numero');
        $sourceParDefautContact = 'Contact (auto)'; // Source par défaut si un nouveau TelEntreprise est créé pour un contact

        // 1. Chercher un enregistrement TelephoneEntreprise existant.
        // Priorité : (entreprise_id, numero, contact_id = $contactId)
        // Sinon    : (entreprise_id, numero, contact_id = NULL)
        $telephone = TelephoneEntreprise::where('entreprise_id', $entrepriseId)
            ->where('numero', $numero)
            ->where(function ($query) use ($contactId) {
                $query->where('contact_id', $contactId)
                      ->orWhereNull('contact_id');
            })
            ->orderByRaw('CASE WHEN contact_id = ? THEN 0 ELSE 1 END', [$contactId]) // Donne la priorité à celui déjà lié au contact
            ->first();

        if ($telephone) {
            // Numéro existant trouvé.
            // S'il était générique (contact_id null) et qu'on le lie maintenant à un contact spécifique.
            if (is_null($telephone->contact_id) && !is_null($contactId)) {
                $telephone->contact_id = $contactId;
                // Optionnel : Mettre à jour la source si un numéro générique est "réclamé" par un contact
                // if($telephone->source != $sourceParDefautContact) { // Ou une autre logique pour la source
                //     $telephone->source = $sourceParDefautContact;
                // }
                $telephone->save();
                Log::info("[getOrCreateForContact] Téléphone existant #{$telephone->id} (était générique) maintenant associé au contact #{$contactId}.");
            } elseif ($telephone->contact_id == $contactId) {
                 Log::info("[getOrCreateForContact] Téléphone existant #{$telephone->id} déjà correctement lié au contact #{$contactId}.");
            } else {
                // Ce cas signifie que le numéro existe mais est lié à un *autre* contact.
                // Selon votre logique métier, vous pourriez :
                // 1. Créer un NOUVEL enregistrement TelephoneEntreprise pour le contact actuel (ce que fera la section 'else' plus bas si $telephone reste null pour ce scope).
                // 2. Retourner une erreur / avertissement.
                // 3. Réassigner (non recommandé sans confirmation).
                // La logique actuelle (avec le first() et le orderByRaw) devrait prioriser celui du bon contact.
                // Si on arrive ici, c'est que le `first()` a trouvé un numéro avec un contact_id différent
                // mais qui correspondait au `orWhereNull('contact_id')` et a été trié après.
                // Pour éviter de créer un doublon si on ne veut qu'un seul enregistrement par (entreprise_id, numero),
                // il faudrait peut-être re-penser la condition de création.
                // Mais pour l'instant, si le $telephone trouvé a un contact_id différent, la logique de création plus bas s'appliquera pour le nouveau contactId
                // SI la recherche initiale n'avait pas retourné celui du contactId actuel.
                // La requête actuelle devrait bien gérer cela en trouvant celui du contact d'abord.
                // On va donc juste logguer si on trouve un numéro qui ne correspond pas au contact_id
                // après la priorisation (ce qui ne devrait pas arriver souvent avec le orderByRaw).
                 Log::info("[getOrCreateForContact] Téléphone existant #{$telephone->id} trouvé, mais lié à un autre contact ({$telephone->contact_id}). La création d'un nouveau pour contact #{$contactId} sera envisagée si aucun autre match prioritaire n'a été fait.");
                 // Pour être plus strict et éviter de créer un nouveau si le numéro existe déjà pour un autre contact :
                 // $telephone = null; // Forcer la création d'un nouveau spécifique à CE contactId
                 // Ou bien, si un numéro ne peut exister qu'une fois par entreprise :
                 // return response()->json(['success' => false, 'message' => 'Ce numéro est déjà assigné à un autre contact.'], 409);
                 // La logique de création ci-dessous va créer un nouvel enregistrement si $telephone est "écrasé" par null ici,
                 // ou si la première recherche n'a rien trouvé.
                 // Pour la logique actuelle : on a trouvé un $telephone. S'il n'est pas lié au bon contactId, on le lie. S'il est déjà lié, c'est bon.
            }
        } else {
            // 2. S'il n'existe pas, le créer et l'associer directement au contact.
            $telephone = TelephoneEntreprise::create([
                'entreprise_id' => $entrepriseId,
                'contact_id' => $contactId,
                'numero' => $numero,
                'source' => $sourceParDefautContact,
                'est_primaire' => 0,
                'etat_verification' => 'non_verifie',
                'derniere_verification_at' => null,
            ]);
            Log::info("[getOrCreateForContact] Nouveau téléphone #{$telephone->id} créé pour contact #{$contactId}, entreprise #{$entrepriseId}.");
        }

        return response()->json([
            'success' => true,
            'message' => 'Enregistrement téléphone du contact traité.',
            'telephone_entreprise_id' => $telephone->id,
            'etat_verification' => $telephone->etat_verification, // Renvoyer l'état actuel
        ]);
    }
}