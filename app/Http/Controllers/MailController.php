<?php

namespace App\Http\Controllers;

use Throwable;
use App\Mail\GenericEmail;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Models\EmailEntreprise;
use App\Models\EchantillonEnquete;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller
{
    /**
     * Envoie un email en utilisant les données du formulaire et en choisissant le template selon la langue.
     */
    public function sendBilingualEmail(Request $request) // MODIFICATION : Le nom de la méthode a été changé.
    {
        $validator = Validator::make($request->all(), [
            'destinataire' => 'required|email',
            'langue_mail' => 'required|in:ar,fr',
            'echantillon_id' => 'required|exists:echantillons_enquetes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Données invalides.', 'errors' => $validator->errors()], 422);
        }

        try {
            $langue = $request->langue_mail; // Récupère 'ar' ou 'fr'

            $echantillon = EchantillonEnquete::with(['entreprise', 'enquete'])->find($request->echantillon_id);
            if (!$echantillon || !$echantillon->enquete) {
                return response()->json(['success' => false, 'message' => 'Échantillon ou enquête associée non trouvé.'], 404);
            }
            
            $entreprise = $echantillon->entreprise;
            $enquete = $echantillon->enquete;

            // Préparation des données en fonction de la langue
            $mailData = [];
            if ($langue === 'fr') {
                $mailData['sujet'] = $enquete->titre_mail_fr;
                $mailData['corps'] = $enquete->corps_mail_fr;
            } else { // 'ar' par défaut
                $mailData['sujet'] = $enquete->titre_mail_ar;
                $mailData['corps'] = $enquete->corps_mail_ar;
            }
            
            // Logique pour les pièces jointes
            $filesToAttach = [];
            if ($enquete->piece_jointe_path) {
                $nomDossier = $enquete->piece_jointe_path;
                $dossierRelatif = 'pieces_jointes/' . $nomDossier;
                $directoryPath = public_path($dossierRelatif);

                if (File::isDirectory($directoryPath)) {
                    foreach (File::files($directoryPath) as $file) {
                        $filesToAttach[] = $dossierRelatif . '/' . $file->getFilename();
                    }
                } else {
                    Log::warning('Dossier de pièces jointes non trouvé : ' . $directoryPath);
                }
            }
            $mailData['files_to_attach'] = $filesToAttach;

            // Appel du Mailable en passant la langue sélectionnée
            Mail::to($request->destinataire)
                ->send(new GenericEmail($mailData, $langue, $entreprise));

            Log::info('Envoi de l\'e-mail réussi à ' . $request->destinataire);
             // ✅ 2. DÉBUT DE LA NOUVELLE LOGIQUE : MARQUER L'EMAIL COMME UTILISÉ
            $emailRecord = EmailEntreprise::where('email', $request->destinataire)
                ->where('entreprise_id', $entreprise->id)
                ->first();

            if ($emailRecord) {
                $emailRecord->last_used_at = now(); // On met à jour la date d'utilisation
                $emailRecord->save();
                Log::info("Email record #{$emailRecord->id} marqué comme utilisé.");
            }
            // ✅ FIN DE LA NOUVELLE LOGIQUE
            return response()->json(['success' => true, 'message' => 'E-mail envoyé avec succès !']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Erreur Serveur: Impossible d\'envoyer l\'e-mail.'
            ], 500);
        }
    }
}
