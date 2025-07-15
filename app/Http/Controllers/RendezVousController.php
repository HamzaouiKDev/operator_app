<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use App\Models\EchantillonEnquete;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Entreprise;
use Illuminate\Validation\ValidationException as LaravelValidationException;

class RendezVousController extends Controller
{
   
    public function index(Request $request)
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Vous devez être connecté.');
    }

    $user = Auth::user();
    $searchTerm = $request->input('search_term');
    $dateDebut = $request->input('date_debut');
    $dateFin = $request->input('date_fin');

    $statusesToExclude = ['complet', 'impossible de contacter', 'refus', 'termine', 'refus final'];
    $excludedEchantillonIds = EchantillonEnquete::where('utilisateur_id', $user->id)
        ->whereIn(DB::raw('LOWER(TRIM(statut))'), $statusesToExclude)
        ->pluck('id');

    // Sous-requête pour trouver le dernier ID de RDV pour chaque entreprise
    $latestRdvIdsSubquery = DB::table('rendez_vous as r')
        ->select(DB::raw('MAX(r.id) as last_id'))
        // ✅ CORRIGÉ: Le nom de la table est "echantillons_enquetes"
        ->join('echantillons_enquetes as ee', 'r.echantillon_enquete_id', '=', 'ee.id')
        ->where('r.utilisateur_id', $user->id)
        ->groupBy('ee.entreprise_id');

    $rendezVousQuery = RendezVous::where('utilisateur_id', $user->id)
        ->whereNotIn('echantillon_enquete_id', $excludedEchantillonIds)
        ->whereIn('id', $latestRdvIdsSubquery);

    if ($searchTerm) {
        $rendezVousQuery->whereHas('echantillonEnquete.entreprise', function ($query) use ($searchTerm) {
            $query->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
        });
    }
    if ($dateDebut) { try { $rendezVousQuery->where('heure_rdv', '>=', Carbon::parse($dateDebut)->startOfDay()); } catch(Exception $e) {} }
    if ($dateFin) { try { $rendezVousQuery->where('heure_rdv', '<=', Carbon::parse($dateFin)->endOfDay()); } catch(Exception $e) {} }

    $rendezVous = $rendezVousQuery
        ->with('echantillonEnquete.entreprise')
        ->orderBy('heure_rdv', 'desc')
        ->paginate(10)
        ->appends($request->query());

    $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu', 'Complet'])
                                                      ->where('utilisateur_id', $user->id)
                                                      ->count();
    $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                                       ->count();

    return view('indexRDV', compact('rendezVous', 'nombreEntreprisesRepondues', 'nombreEntreprisesAttribuees'));
}
    

    // ... La méthode showEntreprisePage() reste inchangée ...
    public function showEntreprisePage(Entreprise $entreprise, Request $request)
    {
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

    // ... La méthode store() reste inchangée ...
    public function store(Request $request, $id)
    {
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
     * Affiche les rendez-vous pour aujourd'hui,
     * en ne montrant que le dernier RDV pour chaque entreprise.
     */
   public function aujourdhui(Request $request)
{
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Vous devez être connecté.');
    }

    $user = Auth::user();
    $searchTerm = $request->input('search_entreprise');
    
    $statusesToExclude = ['complet', 'impossible de contacter', 'refus', 'termine', 'refus final'];
    $excludedEchantillonIds = EchantillonEnquete::where('utilisateur_id', $user->id)
        ->whereIn(DB::raw('LOWER(TRIM(statut))'), $statusesToExclude)
        ->pluck('id');

    // Sous-requête pour trouver le dernier ID de RDV pour chaque entreprise pour AUJOURD'HUI
    $latestRdvIdsSubquery = DB::table('rendez_vous as r')
        ->select(DB::raw('MAX(r.id) as last_id'))
        // ✅ CORRIGÉ: Le nom de la table est "echantillons_enquetes"
        ->join('echantillons_enquetes as ee', 'r.echantillon_enquete_id', '=', 'ee.id')
        ->where('r.utilisateur_id', $user->id)
        ->whereDate('r.heure_rdv', Carbon::today())
        ->groupBy('ee.entreprise_id');

    $rendezVousQuery = RendezVous::where('utilisateur_id', $user->id)
        ->whereDate('heure_rdv', Carbon::today())
        ->whereNotIn('echantillon_enquete_id', $excludedEchantillonIds)
        ->whereIn('id', $latestRdvIdsSubquery);

    if ($searchTerm) {
        $rendezVousQuery->whereHas('echantillonEnquete.entreprise', function ($query) use ($searchTerm) {
            $query->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
        });
    }

    $rendezVous = $rendezVousQuery
        ->with('echantillonEnquete.entreprise')
        ->orderBy('heure_rdv', 'asc')
        ->paginate(10, ['*'], 'page_aujourdhui')
        ->appends($request->except('page'));
    
    $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu', 'Complet'])
                                                      ->where('utilisateur_id', $user->id)
                                                      ->count();
    $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                                       ->count();

    return view('aujourdhuiRDV', compact('rendezVous', 'nombreEntreprisesRepondues', 'nombreEntreprisesAttribuees'));
}
}