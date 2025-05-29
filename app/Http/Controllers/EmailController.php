<?php
// app/Http/Controllers/EmailController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailEntreprise; // ✅ CORRECT
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    /**
     * Ajouter un nouvel email pour une entreprise
     */
    public function store(Request $request, $entreprise_id)
    {
        // Validation
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'source' => 'nullable|string|max:255',
            'est_primaire' => 'nullable|boolean'
        ]);

        // Vérifier l'authentification
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        try {
            // Si c'est marqué comme primaire, désactiver les autres emails primaires
            if (isset($validated['est_primaire']) && $validated['est_primaire']) {
                EmailEntreprise::where('entreprise_id', $entreprise_id)
                    ->update(['est_primaire' => false]);
            }

            // Créer le nouvel email
            EmailEntreprise::create([
                'entreprise_id' => $entreprise_id,
                'email' => $validated['email'],
                'source' => $validated['source'] ?? null,
                'est_primaire' => $validated['est_primaire'] ?? false
            ]);

            return redirect()->back()->with('success', "تم إضافة البريد الإلكتروني {$validated['email']} بنجاح!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة البريد الإلكتروني: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un email
     */
    public function destroy($id)
    {
        try {
            $email = EmailEntreprise::findOrFail($id);
            $emailAddress = $email->email;
            $email->delete();

            return response()->json([
                'success' => true,
                'message' => "تم حذف البريد الإلكتروني {$emailAddress} بنجاح!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف البريد الإلكتروني: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un email
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'source' => 'nullable|string|max:255',
            'est_primaire' => 'nullable|boolean'
        ]);

        try {
            $email = EmailEntreprise::findOrFail($id);

            // Si c'est marqué comme primaire, désactiver les autres emails primaires
            if (isset($validated['est_primaire']) && $validated['est_primaire']) {
                EmailEntreprise::where('entreprise_id', $email->entreprise_id)
                    ->where('id', '!=', $id)
                    ->update(['est_primaire' => false]);
            }

            $email->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث البريد الإلكتروني بنجاح!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث البريد الإلكتروني: ' . $e->getMessage()
            ], 500);
        }
    }
}
