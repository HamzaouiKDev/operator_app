<?php

namespace App\Http\Controllers;

use App\Mail\GenericEmail;
use App\Models\EchantillonEnquete;
use App\Models\Entreprise; // IMPORTANT : Importer le modèle Entreprise
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    /**
     * Envoie un email depuis la modale avec toutes les pièces jointes d'un dossier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailFromModal(Request $request)
    {
        $validated = $request->validate([
            'entreprise_id' => 'required|exists:entreprises,id',
            'destinataire' => 'required|email',
            'sujet' => 'required|string|max:255',
            'corps' => 'required|string',
        ]);

        // On récupère l'objet Entreprise pour le passer à la vue de l'email
        $entreprise = Entreprise::find($validated['entreprise_id']);
        
        $filesToAttach = [];
        Log::info('Début du processus d\'envoi d\'e-mail pour ' . $validated['destinataire']);

        $echantillon = EchantillonEnquete::where('entreprise_id', $validated['entreprise_id'])
                                          ->with('enquete')
                                          ->first();

        if ($echantillon && $echantillon->enquete && $echantillon->enquete->piece_jointe_path) {
            $nomDossier = $echantillon->enquete->piece_jointe_path;
            $dossierRelatif = 'pieces_jointes/' . $nomDossier;
            $directoryPath = public_path($dossierRelatif);

            if (File::isDirectory($directoryPath)) {
                $allFiles = File::files($directoryPath);
                foreach ($allFiles as $file) {
                    $filesToAttach[] = $dossierRelatif . '/' . $file->getFilename();
                }
            } else {
                Log::error('ERREUR: Le dossier de pièces jointes n\'a pas été trouvé à l\'emplacement : ' . $directoryPath);
            }
        }

        try {
            // On passe toutes les données nécessaires, y compris l'objet $entreprise
            Mail::to($validated['destinataire'])
                ->send(new GenericEmail(
                    $validated['sujet'],
                    $validated['corps'],
                    $filesToAttach,
                    $entreprise // On ajoute l'entreprise ici
                ));

            Log::info('Envoi de l\'e-mail réussi à ' . $validated['destinataire']);
            return response()->json(['success' => true, 'message' => 'E-mail envoyé avec succès !']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine());
            report($e); 
            
            return response()->json([
                'success' => false, 
                'message' => 'Erreur Serveur: Impossible d\'envoyer l\'e-mail. Veuillez contacter l\'administrateur.'
            ], 500);
        }
    }
}
