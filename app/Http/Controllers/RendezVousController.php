<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use App\Models\EchantillonEnquete;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Exception; // Garder pour la capture générale
use Illuminate\Support\Facades\DB;
use App\Models\Entreprise;
use Illuminate\Validation\ValidationException as LaravelValidationException;

class RendezVousController extends Controller
{
    /**
     * Affiche la liste principale des rendez-vous de l'utilisateur,
     * triés par proximité à l'heure actuelle.
     * Sert la vue 'indexRDV.blade.php'.
     */
    public function index(Request $request)
    {
        Log::info("[RendezVousController@index] Accès par Utilisateur ID: " . (Auth::id() ?? 'Non connecté'));
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();

        // Récupérer les valeurs des filtres depuis la requête
        $searchTerm = $request->input('search_term');
        $dateDebut = $request->input('date_debut');
        $dateFin = $request->input('date_fin');

        $rendezVousQuery = RendezVous::where('utilisateur_id', $user->id)
            ->whereHas('echantillonEnquete.entreprise'); // S'assure que le RDV a un échantillon et une entreprise valides

        // ===================================================================
        // == DÉBUT : FILTRAGE POUR EXCLURE LES STATUTS TERMINÉS/REFUSÉS ==
        // ===================================================================
        $statusesToExclude = ['refus final', 'Complet', 'termine'];
        $rendezVousQuery->whereHas('echantillonEnquete', function ($query) use ($statusesToExclude) {
            $query->whereNotIn('statut', $statusesToExclude);
        });
        // ===================================================================
        // == FIN : FILTRAGE POUR EXCLURE LES STATUTS TERMINÉS/REFUSÉS ==
        // ===================================================================

        // Filtrer par nom d'entreprise (si fourni)
        if ($searchTerm) {
            $rendezVousQuery->whereHas('echantillonEnquete.entreprise', function ($query) use ($searchTerm) {
                $query->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filtrer par date de début (si fournie)
        if ($dateDebut) {
            try {
                $parsedDateDebut = Carbon::parse($dateDebut)->startOfDay();
                $rendezVousQuery->where('heure_rdv', '>=', $parsedDateDebut);
            } catch (\Exception $e) {
                Log::warning("[RendezVousController@index] Format de date_debut invalide: " . $dateDebut . " - Erreur: " . $e->getMessage());
            }
        }

        // Filtrer par date de fin (si fournie)
        if ($dateFin) {
             try {
                $parsedDateFin = Carbon::parse($dateFin)->endOfDay();
                $rendezVousQuery->where('heure_rdv', '<=', $parsedDateFin);
            } catch (\Exception $e) {
                Log::warning("[RendezVousController@index] Format de date_fin invalide: " . $dateFin . " - Erreur: " . $e->getMessage());
            }
        }

        // Tri amélioré pour gérer les valeurs NULL de heure_rdv
        $rendezVousQuery->orderByRaw("CASE WHEN heure_rdv IS NULL THEN 1 ELSE 0 END ASC, ABS(DATEDIFF(second, heure_rdv, GETDATE())) ASC");

        $rendezVous = $rendezVousQuery
            ->with('echantillonEnquete.entreprise') // Charger les relations pour l'affichage
            ->paginate(10)
            ->appends($request->query()); // Conserver tous les paramètres de la requête dans la pagination

        Log::info("[RendezVousController@index] Nombre de RDV trouvés pour Utilisateur ID {$user->id} (recherche: '{$searchTerm}', debut: '{$dateDebut}', fin: '{$dateFin}'): " . $rendezVous->total());

        // Les statistiques globales
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu'])
                                                        ->where('utilisateur_id', $user->id)
                                                        ->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                                         ->count();

        return view('indexRDV', compact(
            'rendezVous',
            'nombreEntreprisesRepondues',
            'nombreEntreprisesAttribuees'
        ));
    }

    /**
     * Affiche la page de détail d'une entreprise spécifique et ses RDVs.
     */
    public function showEntreprisePage(Entreprise $entreprise, Request $request)
    {
        // ... Votre code existant pour cette méthode reste inchangé ...
        Log::info("[RendezVousController@showEntreprisePage] Accès pour Entreprise ID: {$entreprise->id} par Utilisateur ID: " . (Auth::id() ?? 'Non connecté'));
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }
        $user = Auth::user();
        $entreprise->load(['telephones', 'contacts', 'emails']);
        $echantillon = EchantillonEnquete::where('entreprise_id', $entreprise->id)
                                          ->where('utilisateur_id', $user->id)
                                          ->orderBy('updated_at', 'desc')
                                          ->first();
        $peutLancerAppel = $echantillon ? ($echantillon->utilisateur_id === $user->id) : false;
        $rendezVousAffiches = RendezVous::where('utilisateur_id', $user->id)
            ->whereHas('echantillonEnquete', function ($query) use ($entreprise) {
                $query->where('entreprise_id', $entreprise->id);
            })
            ->with('echantillonEnquete')
            ->orderBy('heure_rdv', 'asc')
            ->paginate(5, ['*'], 'page_rdvs_entreprise');

        Log::info("[RendezVousController@showEntreprisePage] Affichage de l'entreprise ID: {$entreprise->id} ({$entreprise->nom_entreprise}) avec {$rendezVousAffiches->total()} RDVs pour l'utilisateur ID {$user->id}");
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu'])
                                                        ->where('utilisateur_id', $user->id)
                                                        ->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                                         ->count();
        $echantillonIdPourJs = $echantillon ? $echantillon->id : null;
        $echantillonEntrepriseIdJson = json_encode($entreprise->id ?? null);
        $echantillonEntrepriseTelephonesJson = json_encode($entreprise->telephones->toArray() ?? []);
        $echantillonContactsJson = json_encode($entreprise->contacts->map(function ($contact) {
            return [
                'id' => $contact->id,
                'prenom' => $contact->prenom,
                'nom' => $contact->nom,
                'poste' => $contact->poste,
                'telephone_principal_contact' => $contact->telephone,
                'etat_verification' => $contact->etat_verification_telephone,
                'telephone_entreprise_id' => null
            ];
        })->toArray() ?? []);

        return view('index', compact(
            'entreprise', 'echantillon', 'rendezVousAffiches', 'peutLancerAppel',
            'nombreEntreprisesRepondues', 'nombreEntreprisesAttribuees',
            'echantillonIdPourJs', 'echantillonEntrepriseIdJson', 'echantillonEntrepriseTelephonesJson', 'echantillonContactsJson'
        ));
    }
    
    /**
     * Stocke un nouveau rendez-vous.
     */
    public function store(Request $request, $id)
    {
        // ... Votre code existant pour cette méthode reste inchangé ...
        Log::info('<<<<< ENTRÉE MÉTHODE STORE POUR RDV - ID ECHANTILLON: ' . $id . ' >>>>>');
        if (!Auth::check()) {
            Log::warning('<<<<< STORE RDV - Utilisateur non authentifié, redirection vers login >>>>>');
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour créer un rendez-vous.');
        }
        $echantillon = EchantillonEnquete::where('id', $id)->where('utilisateur_id', Auth::id())->first();
        if (!$echantillon) {
            Log::error("<<<<< STORE RDV - Échantillon ID {$id} non trouvé ou non autorisé pour Utilisateur ID: " . Auth::id() . " >>>>>");
            return redirect()->back()->with('error', 'Échantillon non valide ou non autorisé.')->withInput();
        }
        try {
            $validatedData = $request->validate([
                'heure_rdv'   => 'required|date',
                'contact_rdv' => 'nullable|string|max:255',
                'notes'       => 'nullable|string',
            ]);
            Log::info('<<<<< STORE RDV - Validation RÉUSSIE >>>>>', $validatedData);
        } catch (LaravelValidationException $e) {
            Log::error('<<<<< STORE RDV - ÉCHEC VALIDATION >>>>>', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput()->with('form_modal_submitted', 'rendezVousModal');
        }
        $dataToCreate = []; 
        $rendezVous = null;
        DB::beginTransaction();
        Log::info('<<<<< STORE RDV - TRANSACTION DÉMARRÉE >>>>>');
        try {
            $dataToCreate = [
                'echantillon_enquete_id' => $id,
                'utilisateur_id'         => Auth::id(),
                'heure_rdv'              => $validatedData['heure_rdv'],
                'contact_rdv'            => $validatedData['contact_rdv']?? 'بدون جهة اتصال محددة',
                'notes'                  => $validatedData['notes'] ?? null,
            ];
            Log::info('<<<<< STORE RDV - Données prêtes pour création >>>>>', $dataToCreate);
            $rendezVous = RendezVous::create($dataToCreate);
            if ($rendezVous && $rendezVous->exists) {
                Log::info('<<<<< STORE RDV - BLOC IF (SUCCÈS ELOQUENT) ATTEINT - RDV ID: ' . $rendezVous->id . ' >>>>>');
                $echantillon->statut = 'un rendez-vous';
                $echantillon->save();
                Log::info("<<<<< STORE RDV - Statut échantillon #{$echantillon->id} mis à jour >>>>>");
                DB::commit();
                Log::info('<<<<< STORE RDV - TRANSACTION VALIDÉE (COMMIT) >>>>>');
                return redirect()->back()->with('success', '✅ Rendez-vous créé avec succès et statut de l\'échantillon mis à jour.');
            } else {
                DB::rollBack();
                Log::error('<<<<< STORE RDV - BLOC ELSE (ÉCHEC CREATE SILENCIEUX) ATTEINT, TRANSACTION ANNULÉE (ROLLBACK) >>>>>', [
                    'data_envoyee' => $dataToCreate, 'resultat_create_exists' => $rendezVous ? $rendezVous->exists : 'null_rdv_object'
                ]);
                return redirect()->back()->with('error', 'La création du rendez-vous semble avoir échoué silencieusement (else).')->withInput()->with('form_modal_submitted', 'rendezVousModal');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('<<<<< STORE RDV - CATCH QUERYEXCEPTION, TRANSACTION ANNULÉE (ROLLBACK) >>>>>', [
                'message' => $e->getMessage(), 'code' => $e->getCode(), 'data' => $dataToCreate
            ]);
            return redirect()->back()->with('error', 'Une erreur de base de données (QueryException) est survenue.')->withInput()->with('form_modal_submitted', 'rendezVousModal');
        } catch (\Exception $e) {
            Log::error('<<<<< STORE RDV - CATCH EXCEPTION GÉNÉRALE, TRANSACTION ANNULÉE (ROLLBACK) >>>>>', [
                'message' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'data' => $dataToCreate
            ]);
            return redirect()->back()->with('error', 'Une erreur inattendue (Exception) est survenue.')->withInput()->with('form_modal_submitted', 'rendezVousModal');
        }
    }
    
    /**
     * Affiche les rendez-vous pour aujourd'hui.
     * Sert la vue 'aujourdhuiRDV.blade.php'.
     */
    public function aujourdhui(Request $request)
    {
        Log::info("[RendezVousController@aujourdhui] Accès par Utilisateur ID: " . (Auth::id() ?? 'Non connecté'));
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        $searchTerm = $request->input('search_entreprise');
        $today = Carbon::today();

        $rendezVousQuery = RendezVous::where('utilisateur_id', $user->id)
            ->whereHas('echantillonEnquete.entreprise') // S'assure que les relations existent
            ->whereDate('heure_rdv', $today);

        // ===================================================================
        // == DÉBUT : FILTRAGE POUR EXCLURE LES STATUTS TERMINÉS/REFUSÉS ==
        // ===================================================================
        $statusesToExclude = ['refus final', 'Complet', 'termine'];
        $rendezVousQuery->whereHas('echantillonEnquete', function ($query) use ($statusesToExclude) {
            $query->whereNotIn('statut', $statusesToExclude);
        });
        // ===================================================================
        // == FIN : FILTRAGE POUR EXCLURE LES STATUTS TERMINÉS/REFUSÉS ==
        // ===================================================================

        if ($searchTerm) {
            $rendezVousQuery->whereHas('echantillonEnquete.entreprise', function ($query) use ($searchTerm) {
                $query->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
            });
        }

        $rendezVous = $rendezVousQuery
            ->with('echantillonEnquete.entreprise')
            ->orderByRaw("CASE WHEN CONVERT(time, heure_rdv) >= CONVERT(time, GETDATE()) THEN 0 ELSE 1 END ASC, ABS(DATEDIFF(second, heure_rdv, GETDATE())) ASC")
            ->paginate(10, ['*'], 'page_aujourdhui')
            ->appends($request->except('page'));

        Log::info("[RendezVousController@aujourdhui] Nombre de RDV pour aujourd'hui trouvés pour Utilisateur ID {$user->id} (recherche: '{$searchTerm}'): " . $rendezVous->total());
        
        // Les statistiques globales
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu'])
                                                        ->where('utilisateur_id', $user->id)
                                                        ->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                                         ->count();

        return view('aujourdhuiRDV', compact(
            'rendezVous', 
            'nombreEntreprisesRepondues', 
            'nombreEntreprisesAttribuees'
        ));
    }
}
