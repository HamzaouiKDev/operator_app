<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use Illuminate\Http\Request;
use App\Models\EchantillonEnquete;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RendezVousController extends Controller
{
      public function index(Request $request) // Injectez Request ici
    {
        Log::info("[RendezVousController@index] Accès par Utilisateur ID: " . (Auth::id() ?? 'Non connecté'));
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        $searchTerm = $request->input('search_entreprise'); // Récupérer le terme de recherche

        // Commencer la requête de base pour les rendez-vous
        $rendezVousQuery = RendezVous::where('utilisateur_id', $user->id)
            ->whereHas('echantillonEnquete', function ($query) use ($user) {
                // L'échantillon lié à ce RDV doit ACTUELLEMENT être assigné à cet utilisateur
                $query->where('utilisateur_id', $user->id);
            });

        // Si un terme de recherche est fourni, filtrer par nom d'entreprise
        if ($searchTerm) {
            $rendezVousQuery->whereHas('echantillonEnquete.entreprise', function ($query) use ($searchTerm) {
                $query->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
            });
        }

        // Exécuter la requête avec les relations, l'ordre et la pagination
        $rendezVous = $rendezVousQuery
            ->with('echantillonEnquete.entreprise') // Charger les relations nécessaires
            ->orderBy('heure_debut', 'desc')        // Ordonner par date de RDV
            ->paginate(10)                         // Paginer les résultats
            ->appends(['search_entreprise' => $searchTerm]); // IMPORTANT: pour que la pagination conserve le filtre de recherche

        Log::info("[RendezVousController@index] Nombre de RDV trouvés pour Utilisateur ID {$user->id} (recherche: '{$searchTerm}'): " . $rendezVous->total());

        // Regrouper les rendez-vous filtrés par entreprise pour l'affichage
        $rendezVousGroupedByEntreprise = $rendezVous->groupBy(function ($rdv) {
            if ($rdv->echantillonEnquete && $rdv->echantillonEnquete->entreprise) {
                return $rdv->echantillonEnquete->entreprise->id;
            }
            return 'sans_entreprise_pour_rdv_' . $rdv->id;
        });
        
        // Ces statistiques sont les statistiques générales de l'utilisateur.
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu']) // Ajusté pour inclure 'répondu'
                                        ->where('utilisateur_id', $user->id)
                                        ->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                        ->count();

        return view('indexRDV', compact(
            'rendezVous', 
            'rendezVousGroupedByEntreprise', 
            'nombreEntreprisesRepondues', 
            'nombreEntreprisesAttribuees'
            // Vous pouvez aussi passer $searchTerm à la vue si besoin: 'searchTerm' => $searchTerm
        ));
    }
    // ... Votre méthode showByEntreprise($rendezVousId) reste importante ...
    // Elle affiche les détails d'un RDV spécifique.
    // Si un utilisateur arrive sur cette page de détail via un lien (par ex. une notification, un ancien marque-page),
    // la logique pour désactiver/cacher le bouton "Lancer appel" (avec la variable $peutLancerAppel)
    // que nous avons discutée précédemment reste pertinente pour cette page de détail.
    public function showByEntreprise($rendezVousId)
    {
        Log::info("[RendezVousController@showByEntreprise] Accès pour RendezVous ID: {$rendezVousId} par Utilisateur ID: " . (Auth::id() ?? 'Non connecté'));
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }
        $user = Auth::user();

        $selectedRdv = RendezVous::with([
                'echantillonEnquete.entreprise.telephones',
                'echantillonEnquete.entreprise.contacts',
                'echantillonEnquete.entreprise.emails'
            ])
            ->where('id', $rendezVousId) // On cherche le RDV spécifique par son ID
            // Optionnel: S'assurer que le RDV est lié à l'utilisateur si c'est une règle métier.
            // ->where('utilisateur_id', $user->id) 
            ->findOrFail($rendezVousId); // findOrFail pour lever une 404 si le RDV n'existe pas

        if (!$selectedRdv->echantillonEnquete || !$selectedRdv->echantillonEnquete->entreprise) {
            Log::error("[RendezVousController@showByEntreprise] Échantillon ou entreprise manquante pour RDV ID: {$rendezVousId}");
            return redirect()->route('rendezvous.index')->with('error', 'Aucune entreprise ou échantillon valide associé à ce rendez-vous.');
        }

        $echantillon = $selectedRdv->echantillonEnquete;
        $entreprise = $echantillon->entreprise;

        $peutLancerAppel = ($echantillon->utilisateur_id === $user->id);

        // Récupérer tous les rendez-vous de l'utilisateur liés à CETTE MÊME ENTREPRISE
        // J'ai renommé la variable $rendezVousDeLEntreprise en $rendezVous pour correspondre à ce que compact() attend
        // et ce que la vue 'index.blade.php' attend probablement pour la pagination.
        $rendezVous = RendezVous::where('utilisateur_id', $user->id) // RDVs de l'utilisateur connecté
            ->whereHas('echantillonEnquete', function ($query) use ($entreprise) {
                $query->where('entreprise_id', $entreprise->id); // Pour cette entreprise spécifique
            })
            ->with('echantillonEnquete') 
            ->orderBy('heure_debut', 'desc')
            ->paginate(5); // Un nombre plus petit pour une page de détail

        // $rendezVousGrouped est utilisé pour grouper les RDVs affichés sur la page de détail
        $rendezVousGrouped = $rendezVous->groupBy('echantillon_enquete_id');
        Log::info("[RendezVousController@showByEntreprise] Nombre de RDV pour l'entreprise {$entreprise->nom_entreprise} (ID: {$entreprise->id}) pour Utilisateur #{$user->id}: " . $rendezVous->total());


        // Statistiques générales de l'utilisateur (les mêmes que sur la page d'accueil)
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu'])
                                        ->where('utilisateur_id', $user->id)
                                        ->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                        ->count();
        
        return view('index', compact( // La vue 'index.blade.php' est utilisée pour afficher le détail
            'echantillon',           // L'échantillon principal cliqué (via le RDV)
            'entreprise',            // L'entreprise de cet échantillon
            'rendezVous',            // ✅ La variable est maintenant correctement nommée '$rendezVous'
            'rendezVousGrouped',     // Les RDV pour cette entreprise, groupés
            'nombreEntreprisesRepondues',
            'nombreEntreprisesAttribuees',
            'peutLancerAppel'        // Pour conditionner le bouton "Lancer Appel"
        ));
    }
    
    

     public function store(Request $request, $id)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour créer un rendez-vous.');
        }

        // Validation des données
        $request->validate([
            'heure_debut' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        try {
            // Création du rendez-vous
            $rendezVous = RendezVous::create([
                'echantillon_enquete_id' => $id,
                'utilisateur_id' => Auth::user()->id,
                'heure_debut' => $request->heure_debut,
                'heure_fin' => date('Y-m-d H:i:s', strtotime($request->heure_debut . ' +1 hour')),
                'statut' => 'planifie',
                'notes' => $request->notes,
            ]);

            // Retour avec un message de succès
            return redirect()->back()->with('success', 'Rendez-vous créé avec succès.');
        } catch (\Exception $e) {
            // En cas d'erreur, retourner un message d'erreur
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création du rendez-vous.');
        }
    }
    public function aujourdhui(Request $request)
    {
        Log::info("[RendezVousController@aujourdhui] Accès par Utilisateur ID: " . (Auth::id() ?? 'Non connecté'));
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();
        $searchTerm = $request->input('search_entreprise'); // Pour la recherche, si vous la conservez

        $today = Carbon::today(); // Obtient la date d'aujourd'hui à minuit

        // Commencer la requête de base
        $rendezVousQuery = RendezVous::where('utilisateur_id', $user->id)
            ->whereHas('echantillonEnquete', function ($query) use ($user) {
                $query->where('utilisateur_id', $user->id);
            })
            ->whereDate('heure_debut', $today); // Filtre pour les RDV dont la date de début est aujourd'hui

        // Appliquer le filtre de recherche par nom d'entreprise (si le terme est fourni)
        if ($searchTerm) {
            $rendezVousQuery->whereHas('echantillonEnquete.entreprise', function ($query) use ($searchTerm) {
                $query->where('nom_entreprise', 'like', '%' . $searchTerm . '%');
            });
        }

        $rendezVous = $rendezVousQuery
            ->with('echantillonEnquete.entreprise')
            ->orderBy('heure_debut', 'asc') // Trier les RDV d'aujourd'hui par heure croissante
            ->paginate(10)
            ->appends($request->except('page')); // Conserve les paramètres de la requête (ex: search_entreprise) pour la pagination

        Log::info("[RendezVousController@aujourdhui] Nombre de RDV pour aujourd'hui trouvés pour Utilisateur ID {$user->id} (recherche: '{$searchTerm}'): " . $rendezVous->total());

        $rendezVousGroupedByEntreprise = $rendezVous->groupBy(function ($rdv) {
            if ($rdv->echantillonEnquete && $rdv->echantillonEnquete->entreprise) {
                return $rdv->echantillonEnquete->entreprise->id;
            }
            return 'sans_entreprise_pour_rdv_' . $rdv->id;
        });
        
        // Statistiques générales (peuvent rester les mêmes ou être adaptées si besoin)
        $nombreEntreprisesRepondues = EchantillonEnquete::whereIn('statut', ['termine', 'répondu'])
                                        ->where('utilisateur_id', $user->id)
                                        ->count();
        $nombreEntreprisesAttribuees = EchantillonEnquete::where('utilisateur_id', $user->id)
                                        ->count();

        // Utiliser une nouvelle vue ou la même avec un titre dynamique.
        // Ici, nous allons supposer une nouvelle vue pour plus de clarté.
        return view('aujourdhuiRDV', compact(
            'rendezVous', 
            'rendezVousGroupedByEntreprise', 
            'nombreEntreprisesRepondues', 
            'nombreEntreprisesAttribuees'
            // Vous pouvez aussi passer $searchTerm si la vue 'aujourdhuiRDV' l'utilise
        ));
    }
}
