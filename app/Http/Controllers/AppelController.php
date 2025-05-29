<?php

namespace App\Http\Controllers;

use App\Models\Appel;
use Illuminate\Http\Request;

class AppelController extends Controller
{
    public function store(Request $request)
    {
        $appel = Appel::create([
            'echantillon_enquete_id' => $request->echantillon_enquete_id,
            'utilisateur_id' => $request->utilisateur_id,
            'heure_debut' => now(),
            'heure_fin' => now(), // Valeur temporaire, mise à jour à la fin
            'statut' => 'en_cours',
            'notes' => null,
        ]);

        return response()->json(['appel_id' => $appel->id, 'message' => 'Appel créé avec succès']);
    }

    public function end(Request $request)
    {
        $appel = Appel::find($request->appel_id);
        if ($appel) {
            $appel->update([
                'heure_fin' => now(),
                'statut' => 'termine'
            ]);
            return response()->json(['message' => 'Appel terminé avec succès']);
        }
        return response()->json(['error' => 'Appel non trouvé'], 404);
    }
}
