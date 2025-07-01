<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactEntreprise;
use App\Models\EmailEntreprise;
use App\Models\Entreprise;
use App\Models\TelephoneEntreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class EntrepriseImportController extends Controller
{
    /**
     * Affiche la page d'importation avec les formulaires.
     */
    public function create()
    {
        return view('admin.entreprises.import');
    }

    /**
     * Traite le fichier Excel des entreprises.
     */
    public function store(Request $request)
    {
        set_time_limit(300);
        $request->validate(['file-entreprises' => 'required|mimes:xlsx,xls,csv|max:20480']);

        $file = $request->file('file-entreprises');
        $dataChunk = [];
        $chunkSize = 100;
        $totalUpsertedCount = 0;
        $processedRowCount = 0;
        $skippedCount = 0;

        try {
            DB::unprepared('SET IDENTITY_INSERT entreprises ON');

            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $header = [];
            foreach ($worksheet->getRowIterator(1, 1) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $header[] = strtolower(trim($cell->getValue()));
                }
            }

            $updateColumns = [
                'nom_entreprise', 'code_national', 'libelle_activite',
                'gouvernorat_id', 'ville', 'numero_rue', 'nom_rue',
                'statut', 'adresse_cnss', 'localite_cnss'
            ];

            foreach ($worksheet->getRowIterator(2) as $row) {
                $processedRowCount++;
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $rowAssoc = array_combine($header, array_pad($rowData, count($header), null));

                $entident = $rowAssoc['entident'] ?? null;
                if (empty($entident) || empty($rowAssoc['rs'])) {
                    $skippedCount++;
                    continue;
                }

                $dataChunk[] = [
                    'id'               => $entident,
                    'nom_entreprise'   => $rowAssoc['rs'],
                    'code_national'    => $rowAssoc['nat09_2023'],
                    'libelle_activite' => $rowAssoc['lib_actp'] ?? 'Non spécifié',
                    'gouvernorat_id'   => (int) ($rowAssoc['gouvernorat_2023'] ?? 0),
                    'ville'            => $rowAssoc['ville_r'] ?? 'Non spécifié',
                    'numero_rue'       => $rowAssoc['rue_r'] ?? null,
                    'nom_rue'          => $rowAssoc['rue_r'] ?? null,
                    'statut'           => $rowAssoc['statut_2023'] ?? 'active',
                    'adresse_cnss'     => $rowAssoc['adresse_cnss'] ?? null,
                    'localite_cnss'    => $rowAssoc['localite_cnss'] ?? null,
                ];

                if (count($dataChunk) >= $chunkSize) {
                    Entreprise::upsert($dataChunk, ['id'], $updateColumns);
                    $totalUpsertedCount += count($dataChunk);
                    $dataChunk = [];
                }
            }

            if (!empty($dataChunk)) {
                Entreprise::upsert($dataChunk, ['id'], $updateColumns);
                $totalUpsertedCount += count($dataChunk);
            }

        } catch (Throwable $e) {
            DB::unprepared('SET IDENTITY_INSERT entreprises OFF');
            return redirect()->back()->with('error_entreprises', 'Erreur durant l\'importation des entreprises. Erreur : ' . $e->getMessage());
        }

        DB::unprepared('SET IDENTITY_INSERT entreprises OFF');

        $message = "Importation d'entreprises terminée ! **{$totalUpsertedCount} entreprises** ont été créées ou mises à jour.";
        if ($skippedCount > 0) {
            $message .= " Attention : **{$skippedCount} lignes** sur {$processedRowCount} ont été ignorées.";
        }
        return redirect()->route('admin.entreprises.import.form')->with('success_entreprises', $message);
    }

    /**
     * Traite le fichier Excel des téléphones.
     */
    public function storeTelephones(Request $request)
    {
        set_time_limit(300);
        $request->validate(['file-telephones' => 'required|mimes:xlsx,xls,csv|max:20480']);

        $file = $request->file('file-telephones');
        $chunkSize = 1000;
        $dataChunk = [];
        $totalImported = 0;
        $totalSkipped = 0;

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $header = [];
            foreach ($worksheet->getRowIterator(1, 1) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $header[] = strtolower(trim($cell->getValue()));
                }
            }

            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $dataChunk[] = array_combine($header, array_pad($rowData, count($header), null));

                if (count($dataChunk) >= $chunkSize) {
                    list($imported, $skipped) = $this->processAndInsertTelephones($dataChunk);
                    $totalImported += $imported;
                    $totalSkipped += $skipped;
                    $dataChunk = [];
                }
            }

            if (!empty($dataChunk)) {
                list($imported, $skipped) = $this->processAndInsertTelephones($dataChunk);
                $totalImported += $imported;
                $totalSkipped += $skipped;
            }

        } catch (Throwable $e) {
            return redirect()->back()->with('error_telephones', 'Erreur durant l\'importation des téléphones. Erreur : ' . $e->getMessage());
        }

        $message = "Importation des téléphones réussie ! **{$totalImported} numéros** ont été ajoutés ou mis à jour.";
        if ($totalSkipped > 0) {
            $message .= " Attention : **{$totalSkipped} lignes** ont été ignorées.";
        }

        return redirect()->route('admin.entreprises.import.form')->with('success_telephones', $message);
    }

    private function processAndInsertTelephones(array $chunkData)
    {
        $entidentsFromFile = array_unique(array_filter(array_column($chunkData, 'entident')));
        if (empty($entidentsFromFile)) {
            return [0, count($chunkData)];
        }
        
        $existingEntidents = collect();
        foreach (array_chunk($entidentsFromFile, 500) as $idChunk) {
            $existingEntidents = $existingEntidents->merge(
                Entreprise::whereIn('id', $idChunk)->pluck('id')
            );
        }
        $existingEntidents = $existingEntidents->flip();

        $dataToUpsert = [];
        $skippedCount = 0;

        foreach ($chunkData as $rowAssoc) {
            $entident = $rowAssoc['entident'] ?? null;
            $telephoneNumber = isset($rowAssoc['telephone']) ? (string)$rowAssoc['telephone'] : null;

            if (empty($entident) || empty($telephoneNumber) || !$existingEntidents->has($entident)) {
                $skippedCount++;
                continue;
            }

            $dataToUpsert[] = [
                'entreprise_id' => $entident,
                'numero' => $telephoneNumber,
                'source' => $rowAssoc['source'] ?? 'import_excel'
            ];
        }

        if (!empty($dataToUpsert)) {
            foreach(array_chunk($dataToUpsert, 200) as $upsertChunk) {
                 TelephoneEntreprise::upsert($upsertChunk, ['entreprise_id', 'numero'], ['source']);
            }
        }

        return [count($dataToUpsert), $skippedCount];
    }

    /**
     * Traite le fichier Excel des emails.
     */
   public function storeEmails(Request $request)
    {
        set_time_limit(300);
        $request->validate(['file-emails' => 'required|mimes:xlsx,xls,csv|max:20480']);

        $file = $request->file('file-emails');
        $chunkSize = 1000;
        $dataChunk = [];
        $totalImported = 0;
        $allSkippedRows = []; // Pour collecter toutes les lignes ignorées avec leurs raisons

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $header = [];
            foreach ($worksheet->getRowIterator(1, 1) as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $header[] = strtolower(trim($cell->getValue()));
                }
            }

            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $dataChunk[] = array_combine($header, array_pad($rowData, count($header), null));

                if (count($dataChunk) >= $chunkSize) {
                    list($imported, $skipped) = $this->processAndInsertEmails($dataChunk);
                    $totalImported += $imported;
                    $allSkippedRows = array_merge($allSkippedRows, $skipped);
                    $dataChunk = [];
                }
            }
            
            if (!empty($dataChunk)) {
                list($imported, $skipped) = $this->processAndInsertEmails($dataChunk);
                $totalImported += $imported;
                $allSkippedRows = array_merge($allSkippedRows, $skipped);
            }

        } catch (Throwable $e) {
            return redirect()->back()->with('error_emails', 'Erreur durant l\'importation des emails. Erreur : ' . $e->getMessage());
        }

        $totalSkipped = count($allSkippedRows);
        $message = "Importation des emails réussie ! **{$totalImported} adresses** ont été ajoutées ou mises à jour.";
        if ($totalSkipped > 0) {
            $message .= " Attention : **{$totalSkipped} lignes** ont été ignorées.";
        }

        // On renvoie les lignes ignorées à la vue
        return redirect()->route('admin.entreprises.import.form')
            ->with('success_emails', $message)
            ->with('skipped_emails', $allSkippedRows);
    }

    /**
     * Fonction privée pour traiter et insérer les emails, en retournant les détails des échecs.
     */
    private function processAndInsertEmails(array $chunkData)
    {
        $entidentsFromFile = array_unique(array_filter(array_column($chunkData, 'entident')));
        if (empty($entidentsFromFile)) {
            return [0, $chunkData]; // Retourne toutes les lignes comme ignorées
        }
        
        $existingEntidents = Entreprise::whereIn('id', $entidentsFromFile)->pluck('id')->flip();
        
        $dataToUpsert = [];
        $skippedRows = []; // Pour stocker les détails des échecs

        foreach ($chunkData as $rowAssoc) {
            $entident = $rowAssoc['entident'] ?? null;
            $emailAddress = $rowAssoc['email'] ?? null;

            if (empty($entident)) {
                $skippedRows[] = ['email' => $emailAddress ?: 'Vide', 'raison' => 'ID entreprise manquant'];
                continue;
            }
            if (empty($emailAddress)) {
                $skippedRows[] = ['entident' => $entident, 'email' => 'Vide', 'raison' => 'Adresse email manquante'];
                continue;
            }
            if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                $skippedRows[] = ['entident' => $entident, 'email' => $emailAddress, 'raison' => 'Format email invalide'];
                continue;
            }
            if (!$existingEntidents->has($entident)) {
                $skippedRows[] = ['entident' => $entident, 'email' => $emailAddress, 'raison' => 'Entreprise non trouvée en BDD'];
                continue;
            }

            $dataToUpsert[] = [
                'entreprise_id' => $entident,
                'email' => $emailAddress,
                'source' => $rowAssoc['source'] ?? 'import_excel'
            ];
        }

        if (!empty($dataToUpsert)) {
            foreach(array_chunk($dataToUpsert, 200) as $upsertChunk) {
                EmailEntreprise::upsert($upsertChunk, ['entreprise_id', 'email'], ['source']);
            }
        }
        
        return [count($dataToUpsert), $skippedRows];
    }
    /**
     * Traite le fichier Excel des contacts.
     */
    public function storeContacts(Request $request)
    {
        set_time_limit(300);

        $request->validate([
            'file-contacts' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $file = $request->file('file-contacts');
        $totalImported = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;
        $skippedIds = [];

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            
            $headerRow = $worksheet->getRowIterator(1)->current();
            $columnMap = [];
            foreach ($headerRow->getCellIterator() as $cell) {
                $columnMap[strtolower(trim($cell->getValue()))] = $cell->getColumn();
            }

            $requiredColumns = ['identifiant', 'nom', 'prenom'];
            foreach ($requiredColumns as $column) {
                if (!isset($columnMap[$column])) {
                    throw new \Exception("Colonne requise manquante : {$column}");
                }
            }
            
            $dataChunk = [];
            $chunkSize = 500;

            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowIndex = $row->getRowIndex();
                $getCellValue = fn($colName) => isset($columnMap[$colName]) ? trim($worksheet->getCell($columnMap[$colName] . $rowIndex)->getValue()) : null;

                $dataChunk[] = [
                    'entident'  => $getCellValue('identifiant'),
                    'nom'       => $getCellValue('nom'),
                    'prenom'    => $getCellValue('prenom'),
                    'civilite'  => $getCellValue('civilite'),
                    'poste'     => $getCellValue('fonction'),
                    'telephone' => $getCellValue('telephone'),
                    'email'     => $getCellValue('email'),
                ];
                
                if (count($dataChunk) >= $chunkSize) {
                    list($imported, $updated, $skipped, $newSkippedIds) = $this->processAndInsertContacts($dataChunk);
                    $totalImported += $imported;
                    $totalUpdated += $updated;
                    $totalSkipped += $skipped;
                    $skippedIds = array_merge($skippedIds, $newSkippedIds);
                    $dataChunk = [];
                }
            }

            if (!empty($dataChunk)) {
                list($imported, $updated, $skipped, $newSkippedIds) = $this->processAndInsertContacts($dataChunk);
                $totalImported += $imported;
                $totalUpdated += $updated;
                $totalSkipped += $skipped;
                $skippedIds = array_merge($skippedIds, $newSkippedIds);
            }

        } catch (Throwable $e) {
            Log::error("Erreur d'importation des contacts : " . $e->getMessage());
            return redirect()->back()->with('error_contacts', "Une erreur est survenue : " . $e->getMessage());
        }

        $message = "Importation des contacts terminée !<br>";
        $message .= "<strong>{$totalImported}</strong> contacts ajoutés.<br>";
        $message .= "<strong>{$totalUpdated}</strong> contacts mis à jour.<br>";
        if ($totalSkipped > 0) {
            $message .= "<strong>{$totalSkipped}</strong> lignes ont été ignorées.<br>";
            if (!empty($skippedIds)) {
                $message .= "<small>Exemples d'ID d'entreprises non trouvés : " . implode(', ', array_slice($skippedIds, 0, 5)) . "...</small>";
            }
        }

        return redirect()->route('admin.entreprises.import.form')->with('success_contacts', $message);
    }

    private function processAndInsertContacts(array $chunkData)
    {
        $allEntidentsFromFile = array_unique(array_filter(array_column($chunkData, 'entident')));
        if (empty($allEntidentsFromFile)) {
            return [0, 0, count($chunkData), []];
        }
        
        $numericEntidents = array_filter($allEntidentsFromFile, 'is_numeric');

        $existingEntidents = collect();
        if (!empty($numericEntidents)) {
            $existingEntidents = Entreprise::whereIn('id', $numericEntidents)->pluck('id')->flip();
        }
        
        $importedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $skippedIds = [];

        foreach ($chunkData as $row) {
            $entrepriseId = $row['entident'] ?? null;
            $nom = $row['nom'] ?? null;
            $prenom = $row['prenom'] ?? null;
            $email = !empty($row['email']) ? trim($row['email']) : null;

            if (empty($entrepriseId) || !is_numeric($entrepriseId) || empty($nom) || empty($prenom) || !$existingEntidents->has($entrepriseId)) {
                $skippedCount++;
                if (is_numeric($entrepriseId)) {
                    $skippedIds[] = $entrepriseId;
                }
                continue;
            }

            $contactData = [
                'nom'       => $nom,
                'prenom'    => $prenom,
                'civilite'  => $row['civilite'],
                'poste'     => $row['poste'],
                'telephone' => $row['telephone'],
            ];
            
            if (empty($email)) {
                 ContactEntreprise::create(array_merge($contactData, ['entreprise_id' => $entrepriseId]));
                 $importedCount++;
                 continue;
            }

            $contact = ContactEntreprise::updateOrCreate(
                [
                    'entreprise_id' => $entrepriseId,
                    'email'         => $email,
                ],
                $contactData
            );

            if ($contact->wasRecentlyCreated) {
                $importedCount++;
            } else {
                $updatedCount++;
            }
        }
        
        return [$importedCount, $updatedCount, $skippedCount, array_unique($skippedIds)];
    }
}
