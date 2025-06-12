<?php

namespace App\Http\Controllers;

use App\Mail\GenericEmail;
use App\Models\EchantillonEnquete;
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

        $filesToAttach = [];
        Log::info('Début du processus d\'envoi d\'e-mail pour ' . $validated['destinataire']);

        // ** CORRECTION DE LA LOGIQUE DE RECHERCHE **
        // On trouve le premier échantillon pour cette entreprise et on charge son enquête,
        // quel que soit le statut de cette dernière.
        $echantillon = EchantillonEnquete::where('entreprise_id', $validated['entreprise_id'])
                                            ->with('enquete') // On s'assure de charger la relation
                                            ->first();

        if ($echantillon && $echantillon->enquete) {
            Log::info('Enquête trouvée avec ID: ' . $echantillon->enquete->id . ' et statut: "' . $echantillon->enquete->statut . '"');
            
            if ($echantillon->enquete->piece_jointe_path) {
                $nomDossier = $echantillon->enquete->piece_jointe_path;
                Log::info('Valeur du champ "piece_jointe_path" : "' . $nomDossier . '"');

                $dossierRelatif = 'pieces_jointes/' . $nomDossier;
                $directoryPath = public_path($dossierRelatif);
                Log::info('Chemin complet du dossier recherché : ' . $directoryPath);

                if (File::isDirectory($directoryPath)) {
                    Log::info('SUCCESS: Le dossier a été trouvé.');
                    $allFiles = File::files($directoryPath);
                    
                    if (empty($allFiles)) {
                        Log::warning('Le dossier existe mais est vide.');
                    } else {
                        Log::info(count($allFiles) . ' fichier(s) trouvé(s).');
                    }

                    foreach ($allFiles as $file) {
                        $cheminCompletFichier = $dossierRelatif . '/' . $file->getFilename();
                        $filesToAttach[] = $cheminCompletFichier;
                        Log::info('Fichier ajouté à la liste d\'envoi : ' . $cheminCompletFichier);
                    }
                } else {
                    Log::error('ERREUR: Le dossier de pièces jointes n\'a pas été trouvé à l\'emplacement : ' . $directoryPath);
                }
            } else {
                Log::warning('Le champ "piece_jointe_path" est vide (NULL) pour l\'enquête ID ' . $echantillon->enquete->id);
            }
        } else {
            Log::error('ERREUR: Impossible de trouver un échantillon pour l\'entreprise ID ' . $validated['entreprise_id']);
        }

        try {
            Mail::to($validated['destinataire'])
                ->send(new GenericEmail($validated['sujet'], $validated['corps'], $filesToAttach));

            Log::info('Envoi de l\'e-mail réussi à ' . $validated['destinataire'] . ' avec ' . count($filesToAttach) . ' pièce(s) jointe(s).');
            return response()->json(['success' => true, 'message' => 'E-mail envoyé avec succès !']);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
            report($e); 
            
            return response()->json([
                'success' => false, 
                'message' => 'Erreur lors de l\'envoi. Veuillez vérifier les logs pour plus de détails.'
            ], 500);
        }
    }
}
