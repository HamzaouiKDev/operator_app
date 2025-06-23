<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EnqueteController extends Controller
{
    public function index()
    {
        $enquetes = Enquete::latest()->paginate(15);
        return view('admin.enquetes.index', compact('enquetes'));
    }

    public function show(Enquete $enquete)
    {
        return response()->json($enquete);
    }

    /**
     * Enregistre une nouvelle enquête.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255|unique:enquetes,titre',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:brouillon,active,terminee',
            'piece_jointe' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:10240',
            'titre_mail' => 'nullable|string|max:255',
            'corps_mail' => 'nullable|string',
        ]);

        $enquete = new Enquete($validatedData);

        // --- CORRECTION DE LA LOGIQUE D'AJOUT ---
        if ($request->hasFile('piece_jointe')) {
            // S'il y a un fichier, on le stocke
            $file = $request->file('piece_jointe');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('mes_pieces_jointes', $fileName, 'pieces_jointes');
            // Et on enregistre son nom
            $enquete->piece_jointe_path = $fileName;
        } else {
            // S'il n'y a pas de fichier, on met la valeur par défaut
            $enquete->piece_jointe_path = 'mes_pieces_jointes';
        }

        $enquete->save();

        return redirect()->route('admin.enquetes.index')->with('success', 'تم إنشاء المسح بنجاح.');
    }

    /**
     * Met à jour une enquête existante.
     */
    public function update(Request $request, Enquete $enquete)
    {
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255|unique:enquetes,titre,' . $enquete->id,
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:brouillon,active,terminee',
            'piece_jointe' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:10240',
            'titre_mail' => 'nullable|string|max:255',
            'corps_mail' => 'nullable|string',
        ]);

        $enquete->fill($validatedData);

        if ($request->hasFile('piece_jointe')) {
            // On supprime l'ancien fichier s'il en avait un
            if ($enquete->piece_jointe_path && $enquete->piece_jointe_path !== 'mes_pieces_jointes') {
                Storage::disk('pieces_jointes')->delete('mes_pieces_jointes/' . $enquete->piece_jointe_path);
            }
            
            // On stocke le nouveau fichier
            $file = $request->file('piece_jointe');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('mes_pieces_jointes', $fileName, 'pieces_jointes');
            $enquete->piece_jointe_path = $fileName;
        }
        
        $enquete->save();

        return redirect()->route('admin.enquetes.index')->with('success', 'تم تحديث المسح بنجاح.');
    }

    /**
     * Supprime une enquête et sa pièce jointe.
     */
    public function destroy(Enquete $enquete)
    {
        // On supprime le fichier uniquement si ce n'est pas la valeur par défaut
        if ($enquete->piece_jointe_path && $enquete->piece_jointe_path !== 'mes_pieces_jointes') {
            Storage::disk('pieces_jointes')->delete('mes_pieces_jointes/' . $enquete->piece_jointe_path);
        }
        $enquete->delete();
        return redirect()->route('admin.enquetes.index')->with('success', 'تم حذف المسح بنجاح.');
    }
}
