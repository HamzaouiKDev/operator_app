<?php

namespace App\Http\Controllers;

use App\Models\TelephoneEntreprise;
use Illuminate\Http\Request;

class TelephoneController extends Controller
{
    public function store(Request $request, $entreprise_id)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
        ]);

        TelephoneEntreprise::create([
            'entreprise_id' => $entreprise_id,
            'numero' => $request->numero,
            'source' => $request->source,
            'est_primaire' => $request->est_primaire ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Numéro de téléphone ajouté avec succès.');
    }
}
