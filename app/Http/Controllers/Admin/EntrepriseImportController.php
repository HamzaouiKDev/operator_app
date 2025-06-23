<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailEntreprise;
use App\Models\Entreprise;
use App\Models\TelephoneEntreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // CORRECTION: Augmente la durée d'exécution pour les gros fichiers.
        set_time_limit(300);
        $request->validate(['file-entreprises' => 'required|mimes:xlsx,xls,csv|max:20480']);

        $file = $request->file('file-entreprises');
        $dataChunk = [];
        // CORRECTION: Réduit la taille des lots pour être compatible avec SQL Server.
        $chunkSize = 100;
        $totalUpsertedCount = 0;
        $processedRowCount = 0;
        $skippedCount = 0;

        try {
            // CORRECTION: Activer l'insertion manuelle des ID pour SQL Server.
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
                    'libelle_activite' => $rowAssoc['activite'] ?? 'Non spécifié',
                    'gouvernorat_id'   => (int) ($rowAssoc['gouvernorat_2023'] ?? 0),
                    'ville'            => $rowAssoc['ville'] ?? 'Non spécifié',
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

        // CORRECTION: Découpe l'opération d'insertion (upsert) en plus petits lots.
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
                    list($imported, $skipped) = $this->processAndInsertEmails($dataChunk);
                    $totalImported += $imported;
                    $totalSkipped += $skipped;
                    $dataChunk = [];
                }
            }
            
            if (!empty($dataChunk)) {
                list($imported, $skipped) = $this->processAndInsertEmails($dataChunk);
                $totalImported += $imported;
                $totalSkipped += $skipped;
            }

        } catch (Throwable $e) {
            return redirect()->back()->with('error_emails', 'Erreur durant l\'importation des emails. Erreur : ' . $e->getMessage());
        }

        $message = "Importation des emails réussie ! **{$totalImported} adresses email** ont été ajoutées ou mises à jour.";
        if ($totalSkipped > 0) {
            $message .= " Attention : **{$totalSkipped} lignes** ont été ignorées.";
        }

        return redirect()->route('admin.entreprises.import.form')->with('success_emails', $message);
    }

    private function processAndInsertEmails(array $chunkData)
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
            $emailAddress = $rowAssoc['email'] ?? null;

            if (empty($entident) || empty($emailAddress) || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL) || !$existingEntidents->has($entident)) {
                $skippedCount++;
                continue;
            }

            $dataToUpsert[] = [
                'entreprise_id' => $entident,
                'email' => $emailAddress,
                'source' => $rowAssoc['source'] ?? 'import_excel'
            ];
        }

        // CORRECTION: Découpe l'opération d'insertion (upsert) en plus petits lots.
        if (!empty($dataToUpsert)) {
            foreach(array_chunk($dataToUpsert, 200) as $upsertChunk) {
                EmailEntreprise::upsert($upsertChunk, ['entreprise_id', 'email'], ['source']);
            }
        }
        
        return [count($dataToUpsert), $skippedCount];
    }
}
