<?php

namespace App\Http\Controllers;

use App\Models\ContactEntreprise;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request, $entreprise_id)
    {
        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
        ]);

        ContactEntreprise::create([
            'entreprise_id' => $entreprise_id,
            'civilite' => $request->civilite,
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'poste' => $request->poste,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        return redirect()->back()->with('success', 'Contact ajouté avec succès.');
    }
}

