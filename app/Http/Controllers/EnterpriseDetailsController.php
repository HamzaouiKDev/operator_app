<?php

namespace App\Http\Controllers;

use App\Models\EchantillonEnquete;
use App\Models\Entreprise;
use App\Models\TelephoneEntreprise;
use App\Models\ContactEntreprise;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; // Importer la façade File

class EnterpriseDetailsController extends Controller
{
    /**
     * Affiche le prochain échantillon disponible pour traitement.
     */
    public function show()
    {
        // Récupérer le premier échantillon d'enquête disponible avec ses relations clés
        $echantillon = EchantillonEnquete::with([
            'entreprise.contacts.emails', // Charger l'entreprise avec ses contacts et leurs emails
            'entreprise.telephones',    // Charger les téléphones de l'entreprise
            'enquete',                  // Charger les détails de l'enquête associée
            'rendezVous'                // Charger les rendez-vous de l'échantillon
        ])->first();

        if (!$echantillon || !$echantillon->entreprise) {
            // Gérer le cas où il n'y a plus d'échantillons à traiter ou si l'entreprise associée n'existe pas
            return view('empty')->with('message', 'Aucun échantillon d\'enquête disponible pour le moment.');
        }
        
        $entreprise = $echantillon->entreprise;
        $enqueteAssociee = $echantillon->enquete;

        // Les données sont déjà chargées grâce au 'with' (eager loading)
        $telephones = $entreprise->telephones;
        $contacts = $entreprise->contacts;
        $rendezVous = $echantillon->rendezVous;

        // Lister les fichiers disponibles dans le dossier des pièces jointes
        $cheminPiecesJointes = public_path('pieces_jointes_enquetes');
        $fichiersDisponibles = [];
        if (File::isDirectory($cheminPiecesJointes)) {
            $files = File::files($cheminPiecesJointes);
            foreach ($files as $file) {
                $fichiersDisponibles[] = $file->getFilename();
            }
        }

        // Le contrôleur que vous avez fourni retournait 'index', on garde cette logique.
        return view('index', compact(
            'entreprise',
            'telephones',
            'contacts',
            'echantillon',
            'rendezVous',
            'enqueteAssociee',
            'fichiersDisponibles'
        ));
    }

    /**
     * Enregistre un nouveau numéro de téléphone pour une entreprise.
     */
    public function storeTelephone(Request $request, $entreprise_id)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'est_primaire' => 'boolean',
        ]);

        TelephoneEntreprise::create([
            'entreprise_id' => $entreprise_id,
            'numero' => $request->numero,
            'source' => $request->source,
            'est_primaire' => $request->boolean('est_primaire'), // Utiliser la méthode boolean pour les checkboxes
        ]);

        return redirect()->back()->with('success', 'Numéro de téléphone ajouté avec succès.');
    }

    /**
     * Enregistre un nouveau contact pour une entreprise.
     */
    public function storeContact(Request $request, $entreprise_id)
    {
        $request->validate([
            'civilite' => 'nullable|string|max:255',
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:contact_entreprises,email',
            'telephone' => 'nullable|string|max:255',
            'poste' => 'nullable|string|max:255',
        ]);

        ContactEntreprise::create($request->all() + ['entreprise_id' => $entreprise_id]);

        return redirect()->back()->with('success', 'Contact ajouté avec succès.');
    }

    /**
     * Enregistre un nouveau rendez-vous pour un échantillon d'enquête.
     */
    public function storeRendezVous(Request $request, $echantillon_enquete_id)
    {
        $request->validate([
            'heure_rdv' => 'required|date',
            'contact_rdv' => 'required|string|max:255',
            'statut' => 'required|string|in:planifie,confirme,annule', // Rendre la validation plus stricte
            'notes' => 'nullable|string',
        ]);

        RendezVous::create([
            'echantillon_enquete_id' => $echantillon_enquete_id,
            'utilisateur_id' => Auth::id(),
            'heure_rdv' => $request->heure_rdv,
            'contact_rdv' => $request->contact_rdv,
            'statut' => $request->statut,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Rendez-vous ajouté avec succès.');
    }
}
