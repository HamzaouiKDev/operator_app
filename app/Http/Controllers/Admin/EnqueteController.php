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

    /**
     * Affiche le formulaire pour créer une nouvelle enquête.
     */
    public function create()
    {
        $enquete = new Enquete(); // Crée un objet Enquete vide pour le formulaire
        return view('admin.enquetes.create', compact('enquete'));
    }

    /**
     * Affiche le formulaire pour modifier une enquête existante.
     */
    public function edit(Enquete $enquete)
    {
        // LIGNE DE DÉBOGAGE : Affiche les données de l'enquête et arrête le script.
        dd($enquete->toArray()); 

        return view('admin.enquetes.edit', compact('enquete'));
    }

    public function show(Enquete $enquete)
    {
        return response()->json($enquete);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255|unique:enquetes,titre',
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:brouillon,active,terminee',
            'piece_jointe' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:10240',
            'titre_mail_fr' => 'nullable|string|max:255',
            'corps_mail_fr' => 'nullable|string',
            'titre_mail_ar' => 'nullable|string|max:255',
            'corps_mail_ar' => 'nullable|string',
        ]);

        // Utiliser l'assignation manuelle pour être explicite
        $enquete = new Enquete();
        $enquete->titre = $request->input('titre');
        $enquete->description = $request->input('description');
        $enquete->date_debut = $request->input('date_debut');
        $enquete->date_fin = $request->input('date_fin');
        $enquete->statut = $request->input('statut');
        $enquete->titre_mail_fr = $request->input('titre_mail_fr');
        $enquete->corps_mail_fr = $request->input('corps_mail_fr');
        $enquete->titre_mail_ar = $request->input('titre_mail_ar');
        $enquete->corps_mail_ar = $request->input('corps_mail_ar');

        if ($request->hasFile('piece_jointe')) {
            $file = $request->file('piece_jointe');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('mes_pieces_jointes', $fileName, 'pieces_jointes');
            $enquete->piece_jointe_path = $fileName;
        } else {
            $enquete->piece_jointe_path = 'mes_pieces_jointes';
        }

        $enquete->save();

        return redirect()->route('admin.enquetes.index')->with('success', 'تم إنشاء المسح بنجاح.');
    }

    public function update(Request $request, Enquete $enquete)
    {
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255|unique:enquetes,titre,' . $enquete->id,
            'description' => 'nullable|string',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:brouillon,active,terminee',
            'piece_jointe' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip|max:10240',
            'titre_mail_fr' => 'nullable|string|max:255',
            'corps_mail_fr' => 'nullable|string',
            'titre_mail_ar' => 'nullable|string|max:255',
            'corps_mail_ar' => 'nullable|string',
        ]);

        // MODIFICATION : Assignation manuelle de chaque champ pour plus de robustesse
        $enquete->titre = $request->input('titre');
        $enquete->description = $request->input('description');
        $enquete->date_debut = $request->input('date_debut');
        $enquete->date_fin = $request->input('date_fin');
        $enquete->statut = $request->input('statut');
        $enquete->titre_mail_fr = $request->input('titre_mail_fr');
        $enquete->corps_mail_fr = $request->input('corps_mail_fr');
        $enquete->titre_mail_ar = $request->input('titre_mail_ar');
        $enquete->corps_mail_ar = $request->input('corps_mail_ar');

        if ($request->hasFile('piece_jointe')) {
            if ($enquete->piece_jointe_path && $enquete->piece_jointe_path !== 'mes_pieces_jointes') {
                Storage::disk('pieces_jointes')->delete('mes_pieces_jointes/' . $enquete->piece_jointe_path);
            }
            
            $file = $request->file('piece_jointe');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('mes_pieces_jointes', $fileName, 'pieces_jointes');
            $enquete->piece_jointe_path = $fileName;
        }
        
        $enquete->save();

        return redirect()->route('admin.enquetes.index')->with('success', 'تم تحديث المسح بنجاح.');
    }

    public function destroy(Enquete $enquete)
    {
        if ($enquete->piece_jointe_path && $enquete->piece_jointe_path !== 'mes_pieces_jointes') {
            Storage::disk('pieces_jointes')->delete('mes_pieces_jointes/' . $enquete->piece_jointe_path);
        }
        $enquete->delete();
        return redirect()->route('admin.enquetes.index')->with('success', 'تم حذف المسح بنجاح.');
    }
}
