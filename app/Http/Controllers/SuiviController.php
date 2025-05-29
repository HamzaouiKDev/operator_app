<?php

namespace App\Http\Controllers;

use App\Models\Suivi;
use Illuminate\Http\Request;

class SuiviController extends Controller
{
    public function store(Request $request)
    {
        $suivi = Suivi::create([
            'echantillon_enquete_id' => $request->echantillon_enquete_id,
            'utilisateur_id' => $request->utilisateur_id,
            'date_suivi' => now(),
            'commentaire' => 'Relance effectuée',
            'resultat' => 'en_attente',
        ]);

        return response()->json(['message' => 'Relance enregistrée avec succès']);
    }
}

