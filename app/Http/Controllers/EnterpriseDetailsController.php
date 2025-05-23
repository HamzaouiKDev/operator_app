<?php

namespace App\Http\Controllers;

use App\Models\EchantillonEnquete;
use App\Models\Entreprise;
use App\Models\TelephoneEntreprise;
use App\Models\ContactEntreprise;
use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnterpriseDetailsController extends Controller
{
    public function show()
    {
        // Récupérer le premier échantillon d'enquête avec son entreprise
        $echantillon = EchantillonEnquete::with('entreprise')->first();
        
        if (!$echantillon) {
            abort(404, 'Aucun échantillon trouvé');
        }

        // Récupérer les téléphones et contacts de l'entreprise
        $entreprise = $echantillon->entreprise;
        $telephones = TelephoneEntreprise::where('entreprise_id', $entreprise->id)->get();
        $contacts = ContactEntreprise::where('entreprise_id', $entreprise->id)->get();
        $rendezVous = RendezVous::where('echantillon_enquete_id', $echantillon->id)->get();

        return view('entreprise.show', compact('entreprise', 'telephones', 'contacts', 'echantillon', 'rendezVous'));
    }

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
            'est_primaire' => $request->est_primaire ?? 0,
        ]);

        return redirect()->back()->with('success', 'Numéro de téléphone ajouté avec succès');
    }

    public function storeContact(Request $request, $entreprise_id)
    {
        $request->validate([
            'civilite' => 'nullable|string|max:255',
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'nullable|string|max:255',
            'poste' => 'nullable|string|max:255',
        ]);

        ContactEntreprise::create([
            'entreprise_id' => $entreprise_id,
            'civilite' => $request->civilite,
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'poste' => $request->poste,
        ]);

        return redirect()->back()->with('success', 'Contact ajouté avec succès');
    }

    public function storeRendezVous(Request $request, $echantillon_enquete_id)
    {
        $request->validate([
            'heure_debut' => 'required|date',
            'heure_fin' => 'required|date|after:heure_debut',
            'statut' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        RendezVous::create([
            'echantillon_enquete_id' => $echantillon_enquete_id,
            'utilisateur_id' => Auth::id(),
            'heure_debut' => $request->heure_debut,
            'heure_fin' => $request->heure_fin,
            'statut' => $request->statut,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Rendez-vous ajouté avec succès');
    }
}