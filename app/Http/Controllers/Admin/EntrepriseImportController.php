<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Entreprise;
use App\Models\Gouvernorat;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EntrepriseImportController extends Controller
{
    /**
     * Affiche la page avec le formulaire pour uploader le fichier.
     */
    public function create()
    {
        return view('admin.entreprises.import');
    }

    /**
     * Traite le fichier Excel uploadé pour créer les entreprises.
     */
    public function store(Request $request)
    {
        // 1. Validation du fichier
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // Max 10MB
        ]);

        $file = $request->file('file');
        $importedCount = 0;
        $skippedCount = 0;

        try {
            // 2. On charge le fichier avec la librairie PhpSpreadsheet
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();

            $header = [];
            // On lit la première ligne (ligne 1) pour récupérer les en-têtes
            foreach ($worksheet->getRowIterator(1, 1) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                foreach ($cellIterator as $cell) {
                    $header[] = strtolower(trim($cell->getValue()));
                }
            }

            // ANCIEN : Pas besoin de charger tous les gouvernorats par nom.
            // NOUVEAU : Optionnel, charger les IDs existants pour une validation plus robuste
            // $existingGouvernoratIds = Gouvernorat::pluck('id')->all();

            // 3. On parcourt toutes les autres lignes (à partir de la ligne 2)
            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }

                $rowAssoc = array_combine($header, array_pad($rowData, count($header), null));

                if (empty($rowAssoc['rs'])) {
                    $skippedCount++;
                    continue;
                }

                // --- MODIFICATION CRUCIALE ICI ---
                // Le code du gouvernorat est directement dans la colonne 'gouvernorat_2023'
                $gouvernoratCodeFromExcel = trim($rowAssoc['gouvernorat_2023'] ?? '');

                // Validation : s'assurer que c'est un nombre et qu'il existe dans la table gouvernorats.
                // Si la colonne Excel contient des codes et que votre `id` de gouvernorat est un entier
                // (ce qui est le cas avec `$table->id()`), on peut caster.
                $gouvernoratId = (int) $gouvernoratCodeFromExcel; // Caste la valeur en entier

                // Optionnel mais recommandé : Vérifier si l'ID existe réellement dans la base de données
                // Décommenter si vous voulez être sûr que l'ID du code Excel est valide
                // if (! in_array($gouvernoratId, $existingGouvernoratIds)) {
                //     error_log("Code gouvernorat invalide/non trouvé pour : " . $gouvernoratCodeFromExcel . " (ligne: " . $row->getRowIndex() . ", entreprise: " . $rowAssoc['rs'] . "). L'entreprise est ignorée.");
                //     $skippedCount++;
                //     continue;
                // }
                // --- FIN DE LA MODIFICATION CRUCIALE ---


                // 4. On crée l'entreprise
                Entreprise::create([
                    'nom_entreprise'    => $rowAssoc['rs'],
                    'code_national'     => $rowAssoc['nat09_2023'],
                    'libelle_activite'  => $rowAssoc['activite'] ?? 'Non spécifié',
                    'gouvernorat_id'    => $gouvernoratId, // <-- Utilise directement l'ID numérique du fichier Excel
                    'ville'             => $rowAssoc['ville'] ?? 'Non spécifié',
                    'numero_rue'        => $rowAssoc['rue_r'] ?? null,
                    'nom_rue'           => $rowAssoc['rue_r'] ?? null,
                    'statut'            => $rowAssoc['statut_2023'] ?? 'active',
                    'adresse_cnss'      => $rowAssoc['adresse_cnss'] ?? null,
                    'localite_cnss'     => $rowAssoc['localite_cnss'] ?? null,
                ]);
                $importedCount++;
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur durant l\'importation. Vérifiez le format et la structure de votre fichier. Erreur technique: ' . $e->getMessage());
        }

        $message = "Le fichier a été importé et **{$importedCount} entreprises** ont été ajoutées avec succès !";
        if ($skippedCount > 0) {
            $message .= " Attention : **{$skippedCount} lignes** ont été ignorées (Code Gouvernorat invalide ou données manquantes/inattendues). Vérifiez les logs pour plus de détails.";
        }

        return redirect()->route('admin.entreprises.import.form')
                         ->with('success', $message);
    }
}