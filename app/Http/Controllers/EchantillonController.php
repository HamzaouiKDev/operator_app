<?php

namespace App\Http\Controllers;

use App\Models\EchantillonEnquete;
use App\Models\Entreprise;
use App\Models\ContactEntreprise;
use App\Models\TelephoneEntreprise;
use App\Models\Appel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EchantillonController extends Controller
{
    /**
     * Affiche l'échantillon "actuel" de l'utilisateur ou en attribue un nouveau.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }
        Log::info("[EchantillonController@index] Utilisateur connecté: #{$user->id} - {$user->name}");

        $echantillonActuel = EchantillonEnquete::where('utilisateur_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (!$echantillonActuel) {
            Log::info("[EchantillonController@index] Aucun échantillon actuel pour l'utilisateur #{$user->id}, tentative d'attribution...");
            return $this->attribuerNouvelEchantillon($user, false);
        }

        // S'assurer que les relations sont chargées pour l'affichage
        $echantillonActuel->loadMissing([
            'entreprise.telephones',
            'entreprise.contacts',
            'entreprise.emails'
        ]);

        $nomEntreprise = optional($echantillonActuel->entreprise)->nom_entreprise ?? 'N/A';
        Log::info("[EchantillonController@index] Affichage de l'échantillon actuel #{$echantillonActuel->id} pour l'utilisateur #{$user->id}. Entreprise: {$nomEntreprise}");
        return $this->afficherAvecStatistiques($echantillonActuel, $user->id);
    }

    /**
     * Attribue un NOUVEL échantillon à l'utilisateur (en plus de ceux qu'il pourrait déjà avoir)
     * et affiche le nouvel échantillon.
     */
    public function next(Request $request)
    {
        if (!Auth::check()) {
            Log::warning("[EchantillonController@next] Tentative d'accès par utilisateur non authentifié.");
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول للمتابعة.');
        }

        $user = Auth::user();
        Log::info("[EchantillonController@next] Bouton 'Suivant' cliqué par Utilisateur: #{$user->id} ({$user->name}). L'échantillon actuel (s'il existe) NE SERA PAS libéré.");

        // La logique de libération de l'échantillon précédent est supprimée.
        // L'utilisateur conserve ses échantillons précédents et un nouveau lui est attribué si disponible.

        // attribuerNouvelEchantillon gère sa propre transaction et la redirection avec message de succès/erreur.
        // Le `true` indique qu'il doit afficher un message flash lors de la redirection.
        // Cette méthode va chercher un échantillon avec utilisateur_id = null et l'assigner à l'utilisateur.
        return $this->attribuerNouvelEchantillon($user, true); 
    }

      /**
     * **MÉTHODE PRINCIPALE** : Attribution d'un nouvel échantillon
     */
    private function attribuerNouvelEchantillon($user, $afficherMessage = false)
    {
        Log::info("[attribuerNouvelEchantillon] Tentative d'attribution pour Utilisateur #{$user->id}. Afficher message: " . ($afficherMessage ? 'Oui' : 'Non'));
        return DB::transaction(function () use ($user, $afficherMessage) {
            
            $echantillonDisponible = DB::table('echantillons_enquetes')
                ->whereNull('utilisateur_id')
                ->orderByRaw('CASE WHEN priorite = "haute" THEN 1 WHEN priorite = "moyenne" THEN 2 WHEN priorite = "basse" THEN 3 ELSE 4 END')
                ->orderBy('id', 'asc')
                ->lockForUpdate() // Important pour éviter les conditions de concurrence
                ->first();

            if (!$echantillonDisponible) {
                Log::warning("[attribuerNouvelEchantillon] ❌ Aucun échantillon disponible pour attribution à Utilisateur #{$user->id}");
                if ($afficherMessage) {
                    return redirect()->route('echantillons.index')->with('error', 'لا توجد عينات جديدة متاحة في الوقت الحالي.');
                } else {
                    return $this->afficherSansEchantillon($user->id, 'لا توجد عينات متاحة للتخصيص في الوقت الحالي.');
                }
            }

            Log::info("[attribuerNouvelEchantillon] Échantillon disponible trouvé: ID #{$echantillonDisponible->id}");
            $maintenant = now();
            $updated = DB::table('echantillons_enquetes')
                ->where('id', $echantillonDisponible->id)
                // Double vérification pour s'assurer qu'il est toujours non assigné (au cas où lockForUpdate ne serait pas suffisant ou mal configuré)
                ->whereNull('utilisateur_id') 
                ->update([
                    'utilisateur_id' => $user->id,
                    'statut' => 'en attente', // Statut initial lors de l'attribution
                    'updated_at' => $maintenant 
                ]);

            if ($updated) {
                Log::info("[attribuerNouvelEchantillon] ✅ Échantillon #{$echantillonDisponible->id} attribué avec succès à Utilisateur #{$user->id}");
                $nouvelEchantillon = EchantillonEnquete::with([
                    'entreprise', 'entreprise.telephones', 'entreprise.contacts', 'entreprise.emails'
                ])->find($echantillonDisponible->id);

                if (!$nouvelEchantillon) { // Sécurité
                    Log::error("[attribuerNouvelEchantillon]  критична помилка: Не вдалося знайти новопризначений зразок #{$echantillonDisponible->id} після оновлення.");
                     return redirect()->route('echantillons.index')->with('error', 'حدث خطأ داخلي أثناء محاولة عرض العينة الجديدة.');
                }

                if ($afficherMessage) {
                    return redirect()->route('echantillons.index')->with('success', "تم تخصيص عينة جديدة: {$nouvelEchantillon->entreprise->nom_entreprise}");
                } else {
                    return $this->afficherAvecStatistiques($nouvelEchantillon, $user->id);
                }
            } else {
                Log::error("[attribuerNouvelEchantillon] ❌ Échec de la mise à jour (attribution) pour échantillon #{$echantillonDisponible->id}. Peut-être déjà pris ?");
                // L'échantillon a pu être pris par un autre processus entre le `first()` et le `update()`
                // Réessayer ou informer l'utilisateur
                if ($afficherMessage) {
                    return redirect()->route('echantillons.index')->with('error', 'لم يتم العثور على عينة متاحة، ربما تم تخصيصها للتو. يرجى المحاولة مرة أخرى.');
                } else {
                     return $this->afficherSansEchantillon($user->id, 'لم يتم العثور على عينة متاحة، ربما تم تخصيصها للتو. يرجى المحاولة مرة أخرى.');
                }
            }
        });
    }

    /**
     * Affiche la page lorsqu'aucun échantillon n'est attribué à l'utilisateur.
     */
    private function afficherSansEchantillon($userId, $messageArabe)
    {
        Log::info("[afficherSansEchantillon] Pour Utilisateur #{$userId}. Message: {$messageArabe}");
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['répondu', 'termine'])->where('utilisateur_id', $userId)->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $userId)->count();

        return view('index', [
            'echantillon' => null,
            'nombreEntreprisesRepondues' => $nombreEntreprisesRepondues,
            'nombreEntreprisesAttribuees' => $nombreEntreprisesAttribuees,
            'error' => $messageArabe,
            'peutLancerAppel' => false,
            'echantillonEntrepriseIdJson' => json_encode(null),
            'echantillonEntrepriseTelephonesJson' => json_encode([]),
            'echantillonContactsJson' => json_encode([]),
        ]);
    }

    /**
     * Méthode principale pour afficher un échantillon et préparer les données pour la vue.
     */
    private function afficherAvecStatistiques($echantillon, $userId)
    {
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['répondu', 'termine'])->where('utilisateur_id', $userId)->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $userId)->count();

        $echantillon->loadMissing(['entreprise.telephones', 'entreprise.contacts', 'entreprise.emails']);

        $nomEntreprise = optional($echantillon->entreprise)->nom_entreprise ?? 'N/A';
        Log::info("[afficherAvecStatistiques] Préparation de l'affichage pour Échantillon #{$echantillon->id} (Entreprise: {$nomEntreprise}) pour Utilisateur #{$userId}.");

        $entrepriseTelephonesForJs = [];
        $contactsForJs = [];
        $entrepriseIdForJs = null;

        if ($echantillon->entreprise) {
            $entrepriseIdForJs = $echantillon->entreprise->id;

            if ($echantillon->entreprise->telephones) {
                $entrepriseTelephonesForJs = $echantillon->entreprise->telephones->map(function ($tel) {
                    return [
                        'id' => $tel->id,
                        'numero' => $tel->numero,
                        'source' => $tel->source,
                        'est_primaire' => $tel->est_primaire,
                        'etat_verification' => $tel->etat_verification,
                        'contact_id' => $tel->contact_id
                    ];
                })->toArray();
            }

            if ($echantillon->entreprise->contacts) {
                $contactsForJs = $echantillon->entreprise->contacts->map(function ($contact) use ($echantillon) {
                    $telEntrepriseRecord = null;
                    if ($contact->telephone && trim($contact->telephone) !== '') {
                        $telEntrepriseRecord = TelephoneEntreprise::where('entreprise_id', $echantillon->entreprise->id)
                            ->where('numero', $contact->telephone)
                            ->orderByRaw('CASE WHEN contact_id = ? THEN 0 ELSE 1 END', [$contact->id])
                            ->first();
                    }
                    return [
                        'id' => $contact->id,
                        'prenom' => $contact->prenom,
                        'nom' => $contact->nom,
                        'poste' => $contact->poste,
                        'telephone_principal_contact' => $contact->telephone,
                        'telephone_entreprise_id' => $telEntrepriseRecord ? $telEntrepriseRecord->id : null,
                        'etat_verification' => $telEntrepriseRecord ? $telEntrepriseRecord->etat_verification : 'non_verifie',
                    ];
                })->toArray();
            }
        } else {
            Log::warning("[afficherAvecStatistiques] L'échantillon #{$echantillon->id} n'a pas d'entreprise associée.");
        }

        return view('index', [
            'echantillon' => $echantillon,
            'nombreEntreprisesRepondues' => $nombreEntreprisesRepondues,
            'nombreEntreprisesAttribuees' => $nombreEntreprisesAttribuees,
            'peutLancerAppel' => ($echantillon && $echantillon->utilisateur_id == $userId),
            'echantillonEntrepriseIdJson' => json_encode($entrepriseIdForJs),
            'echantillonEntrepriseTelephonesJson' => json_encode($entrepriseTelephonesForJs),
            'echantillonContactsJson' => json_encode($contactsForJs),
        ]);
    }

    /**
     * Met à jour le statut d'un échantillon (appelé par AJAX).
     */
    public function updateStatut(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);

        Log::info("[EchantillonController@updateStatut] Utilisateur #{$user->id} MàJ statut échantillon #{$id}");
        $validated = $request->validate(['statut' => 'required|string|in:répondu,réponse partielle,un rendez-vous,pas de réponse,refus,introuvable,termine,en attente']);

        try {
            $echantillon = EchantillonEnquete::where('id', $id)
                ->where('utilisateur_id', $user->id)
                ->firstOrFail();

            $echantillon->statut = $validated['statut'];
            $echantillon->date_mise_a_jour = now();
            $echantillon->save();

            Log::info("[EchantillonController@updateStatut] ✅ Statut échantillon #{$id} -> '{$validated['statut']}' par Utilisateur #{$user->id}");
            return response()->json(['success' => true, 'message' => 'Statut de l\'échantillon mis à jour.', 'statut' => $validated['statut']]);
        } catch (ModelNotFoundException $e) {
            Log::warning("[EchantillonController@updateStatut] Échantillon #{$id} non trouvé ou non attribué à Utilisateur #{$user->id}");
            return response()->json(['success' => false, 'message' => 'Échantillon non trouvé ou non attribué.'], 404);
        } catch (\Exception $e) {
            Log::error("[EchantillonController@updateStatut] Erreur lors de la mise à jour du statut pour échantillon #{$id}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur lors de la mise à jour du statut.'], 500);
        }
    }

    /**
     * Démarre un enregistrement d'appel.
     */
    public function demarrerAppel(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('[demarrerAppel] Non authentifié.');
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        $validated = $request->validate([
            'echantillon_id' => 'required|exists:echantillons_enquetes,id',
            'telephone_id' => 'nullable|exists:telephones_entreprises,id',
            'numero_appele' => 'required|string|max:255',
            'statut_numero' => 'required|string|in:valide,faux_numero,pas_programme,ne_pas_deranger,non_verifie'
        ]);

        $echantillonId = $validated['echantillon_id'];
        $telephoneDbId = $validated['telephone_id'] ?? null;
        $numeroAppele = $validated['numero_appele'];
        $statutNumeroSelectionne = $validated['statut_numero'];

        Log::info("[demarrerAppel] User #{$user->id}, Éch ID: {$echantillonId}, TelDB ID: {$telephoneDbId}, Num: {$numeroAppele}, StatutNum: {$statutNumeroSelectionne}");

        $echantillonPourAppel = EchantillonEnquete::with('entreprise')
            ->where('id', $echantillonId)
            ->where('utilisateur_id', $user->id)
            ->first();

        if (!$echantillonPourAppel) {
            return response()->json(['success' => false, 'message' => "Échantillon non trouvé ou non attribué."], 404);
        }
        if (!$echantillonPourAppel->entreprise) {
            return response()->json(['success' => false, 'message' => 'Erreur: échantillon sans entreprise.'], 500);
        }

        // Update TelephoneEntreprise status if provided and statut_numero is 'valide'
        if ($telephoneDbId && $statutNumeroSelectionne === 'valide') {
            try {
                $telephoneEnregistrement = TelephoneEntreprise::find($telephoneDbId);
                if ($telephoneEnregistrement) {
                    if ($telephoneEnregistrement->etat_verification !== 'valide') {
                        $telephoneEnregistrement->etat_verification = 'valide';
                        $telephoneEnregistrement->derniere_verification_at = now();
                        $telephoneEnregistrement->save();
                        Log::info("[demarrerAppel] Statut TelephoneEntreprise #{$telephoneDbId} mis à jour à 'valide'.");
                    }
                } else {
                    Log::warning("[demarrerAppel] TelephoneEntreprise #{$telephoneDbId} non trouvé pour mise à jour statut.");
                }
            } catch (\Exception $e) {
                Log::error("[demarrerAppel] Erreur MàJ statut TelephoneEntreprise #{$telephoneDbId}: " . $e->getMessage());
            }
        }

        try {
            // Nettoyer les appels "fantômes" pour cet utilisateur sur d'autres échantillons
            $phantomCalls = Appel::where('utilisateur_id', $user->id)
                ->where('statut', 'en_cours')
                ->where('echantillon_enquete_id', '!=', $echantillonId)
                ->get();

            foreach ($phantomCalls as $call) {
                $call->update([
                    'statut' => 'termine_automatiquement',
                    'heure_fin' => now(),
                    'notes' => ($call->notes ? $call->notes . "\n" : '') . 'Terminé car un nouvel appel a démarré sur un autre échantillon.'
                ]);
                Log::info("[demarrerAppel] Appel fantôme #{$call->id} terminé automatiquement.");
            }

            // Vérifier s'il y a déjà un appel en cours pour CET échantillon par CET utilisateur
            $appelEnCoursExistant = Appel::where('utilisateur_id', $user->id)
                ->where('echantillon_enquete_id', $echantillonId)
                ->where('statut', 'en_cours')
                ->first();

            if ($appelEnCoursExistant) {
                Log::warning("[demarrerAppel] Appel #{$appelEnCoursExistant->id} déjà en cours pour éch #{$echantillonId} par user #{$user->id}. On le termine avant d'en créer un nouveau.");
                $appelEnCoursExistant->update([
                    'statut' => 'termine_erreur',
                    'heure_fin' => now(),
                    'notes' => ($appelEnCoursExistant->notes ? $appelEnCoursExistant->notes . "\n" : '') . 'Terminé (erreur) car un nouvel appel a été démarré sur le même échantillon.'
                ]);
            }

            // Créer le nouvel appel
            $appel = Appel::create([
                'echantillon_enquete_id' => $echantillonPourAppel->id,
                'utilisateur_id' => $user->id,
                'heure_debut' => now(),
                'heure_fin' => now(),
                'statut' => 'en_cours',
                'telephone_utilise_id' => $telephoneDbId,
                'numero_compose' => $numeroAppele,
                'statut_numero_au_moment_appel' => $statutNumeroSelectionne,
                'notes' => "Appel initié vers: " . $numeroAppele
            ]);

            $nomEntrepriseAppel = $echantillonPourAppel->entreprise->nom_entreprise ?? 'N/A';
            Log::info("📞 Nouvel appel DÉMARRÉ - ID: #{$appel->id} | User: #{$user->id} | Éch: #{$echantillonPourAppel->id} | Numéro: {$appel->numero_compose}");

            $appelData = [
                'id' => $appel->id,
                'echantillon_enquete_id' => $appel->echantillon_enquete_id,
                'heure_debut' => $appel->heure_debut->toIso8601String(),
                'notes' => $appel->notes,
                'statut' => $appel->statut,
                'entreprise_nom' => $nomEntrepriseAppel,
                'numero_compose' => $appel->numero_compose
            ];
            return response()->json(['success' => true, 'message' => 'بدأت المكالمة بنجاح!', 'appel' => $appelData]);
        } catch (\Exception $e) {
            Log::error("❌ Erreur Exception Appel::create pour éch. #{$echantillonId}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'حدث خطأ فادح أثناء بدء المكالمة.'], 500);
        }
    }

    /**
     * Termine un appel en cours.
     */
    public function terminerAppel(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('[terminerAppel] Non authentifié.');
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        $validated = $request->validate([
            'appel_id' => 'required|exists:appels,id',
            'notes' => 'nullable|string'
        ]);

        $appelId = $validated['appel_id'];
        $notes = $validated['notes'];

        try {
            $appel = Appel::where('id', $appelId)
                ->where('utilisateur_id', $user->id)
                ->where('statut', 'en_cours')
                ->firstOrFail();

            $appel->update([
                'statut' => 'termine',
                'heure_fin' => now(),
                'notes' => $notes ? ($appel->notes ? $appel->notes . "\n" : '') . $notes : $appel->notes
            ]);

            Log::info("[terminerAppel] Appel #{$appel->id} terminé par Utilisateur #{$user->id}.");
            return response()->json(['success' => true, 'message' => 'انتهت المكالمة بنجاح!']);
        } catch (ModelNotFoundException $e) {
            Log::warning("[terminerAppel] Appel #{$appelId} non trouvé ou non en cours pour Utilisateur #{$user->id}.");
            return response()->json(['success' => false, 'message' => 'Appel non trouvé ou déjà terminé.'], 404);
        } catch (\Exception $e) {
            Log::error("[terminerAppel] Erreur lors de la terminaison de l'appel #{$appelId}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur lors de la terminaison de l\'appel.'], 500);
        }
    }

    /**
     * Vérifie si un appel est en cours pour l'utilisateur.
     */
    public function appelEnCours()
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('[appelEnCours] Non authentifié.');
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        $appel = Appel::where('utilisateur_id', $user->id)
            ->where('statut', 'en_cours')
            ->first();

        if ($appel) {
            $nomEntreprise = EchantillonEnquete::where('id', $appel->echantillon_enquete_id)
                ->with('entreprise')
                ->first()->entreprise->nom_entreprise ?? 'N/A';

            $appelData = [
                'id' => $appel->id,
                'echantillon_enquete_id' => $appel->echantillon_enquete_id,
                'heure_debut' => $appel->heure_debut->toIso8601String(),
                'notes' => $appel->notes,
                'statut' => $appel->statut,
                'entreprise_nom' => $nomEntreprise,
                'numero_compose' => $appel->numero_compose
            ];
            Log::info("[appelEnCours] Appel en cours trouvé: #{$appel->id} pour Utilisateur #{$user->id}.");
            return response()->json(['success' => true, 'appel' => $appelData]);
        }

        Log::info("[appelEnCours] Aucun appel en cours pour Utilisateur #{$user->id}.");
        return response()->json(['success' => false, 'message' => 'Aucun appel en cours.']);
    }

    /**
     * Libère un échantillon spécifique.
     */
    public function liberer($id)
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('[liberer] Non authentifié.');
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        try {
            $echantillon = EchantillonEnquete::where('id', $id)
                ->where('utilisateur_id', $user->id)
                ->firstOrFail();

            $echantillon->utilisateur_id = null;
            $echantillon->statut = in_array($echantillon->statut, ['termine', 'répondu', 'refus', 'introuvable']) ? $echantillon->statut : 'en attente';
            $echantillon->date_liberation = now();
            $echantillon->save();

            Log::info("[liberer] Échantillon #{$id} libéré par Utilisateur #{$user->id}. Nouveau statut: {$echantillon->statut}");
            return response()->json(['success' => true, 'message' => 'Échantillon libéré avec succès.']);
        } catch (ModelNotFoundException $e) {
            Log::warning("[liberer] Échantillon #{$id} non trouvé ou non attribué à Utilisateur #{$user->id}.");
            return response()->json(['success' => false, 'message' => 'Échantillon non trouvé ou non attribué.'], 404);
        } catch (\Exception $e) {
            Log::error("[liberer] Erreur lors de la libération de l'échantillon #{$id}: " . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur lors de la libération.'], 500);
        }
    }

    /**
     * Retourne l'échantillon actuel de l'utilisateur (API).
     */
    public function current()
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('[current] Non authentifié.');
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        $echantillon = EchantillonEnquete::where('utilisateur_id', $user->id)
            ->with(['entreprise.telephones', 'entreprise.contacts', 'entreprise.emails'])
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$echantillon) {
            Log::info("[current] Aucun échantillon actuel pour Utilisateur #{$user->id}.");
            return response()->json(['success' => false, 'message' => 'Aucun échantillon attribué.']);
        }

        Log::info("[current] Échantillon actuel #{$echantillon->id} retourné pour Utilisateur #{$user->id}.");
        return response()->json(['success' => true, 'echantillon' => $echantillon]);
    }

    /**
     * Retourne le nombre d'échantillons disponibles (API).
     */
    public function disponibles()
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('[disponibles] Non authentifié.');
            return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }

        $disponibles = EchantillonEnquete::whereNull('utilisateur_id')
            ->where('statut', 'en attente')
            ->count();

        Log::info("[disponibles] Nombre d'échantillons disponibles: {$disponibles} pour Utilisateur #{$user->id}.");
        return response()->json(['success' => true, 'disponibles' => $disponibles]);
    }
}