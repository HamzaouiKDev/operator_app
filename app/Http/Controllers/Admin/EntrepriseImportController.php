<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailEntreprise; // Ajout nécessaire pour l'import des emails
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
        // Validation spécifique pour le formulaire des entreprises
        $request->validate(['file-entreprises' => 'required|mimes:xlsx,xls,csv|max:20480']);

        $file = $request->file('file-entreprises');
        $dataToImport = [];
        $skippedCount = 0;
        $processedRowCount = 0;
        $totalUpsertedCount = 0;

        if (!defined('IMPORT_CHUNK_SIZE')) {
            define('IMPORT_CHUNK_SIZE', 500);
        }

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

            $updateColumns = [
                'nom_entreprise', 'code_national', 'libelle_activite',
                'gouvernorat_id', 'ville', 'numero_rue', 'nom_rue',
                'statut', 'adresse_cnss', 'localite_cnss'
            ];

            foreach ($worksheet->getRowIterator(2) as $row) {
                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $rowAssoc = array_combine($header, array_pad($rowData, count($header), null));
                $processedRowCount++;

                $entident = $rowAssoc['entident'] ?? null;
                if (empty($entident) || empty($rowAssoc['rs'])) {
                    $skippedCount++;
                    continue;
                }

                $dataToImport[] = [
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

                if (count($dataToImport) >= IMPORT_CHUNK_SIZE) {
                    Entreprise::upsert($dataToImport, ['id'], $updateColumns);
                    $totalUpsertedCount += count($dataToImport);
                    $dataToImport = [];
                }
            }

            if (!empty($dataToImport)) {
                Entreprise::upsert($dataToImport, ['id'], $updateColumns);
                $totalUpsertedCount += count($dataToImport);
            }

        } catch (Throwable $e) {
            return redirect()->back()->with('error_entreprises', 'Erreur durant l\'importation des entreprises. Erreur : ' . $e->getMessage());
        }

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
        // Validation spécifique pour le formulaire des téléphones
        $request->validate(['file-telephones' => 'required|mimes:xlsx,xls,csv|max:20480']);

        $file = $request->file('file-telephones');
        $importedCount = 0;
        $skippedRows = [];

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

            DB::transaction(function () use ($worksheet, $header, &$importedCount, &$skippedRows) {
                foreach ($worksheet->getRowIterator(2) as $rowIndex => $row) {
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $rowAssoc = array_combine($header, array_pad($rowData, count($header), null));

                    $entident = $rowAssoc['entident'] ?? null;
                    $telephoneNumber = $rowAssoc['telephone'] ?? null;

                    if (empty($entident) || empty($telephoneNumber)) {
                        $skippedRows[] = $rowIndex;
                        continue;
                    }

                    $entreprise = Entreprise::find($entident);

                    if ($entreprise) {
                        $entreprise->telephones()->updateOrCreate(
                            ['numero' => $telephoneNumber],
                            ['source' => $rowAssoc['source'] ?? 'import_excel']
                        );
                        $importedCount++;
                    } else {
                        $skippedRows[] = $rowIndex;
                    }
                }
            });
        } catch (Throwable $e) {
            return redirect()->back()->with('error_telephones', 'Erreur durant l\'importation des téléphones. Erreur : ' . $e->getMessage());
        }

        $message = "Importation des téléphones réussie ! **{$importedCount} numéros** ont été ajoutés ou mis à jour.";
        if (!empty($skippedRows)) {
            $skippedCount = count($skippedRows);
            $message .= " Attention : **{$skippedCount} lignes** ont été ignorées (entreprise non trouvée ou données manquantes).";
        }

        return redirect()->route('admin.entreprises.import.form')->with('success_telephones', $message);
    }

    /**
     * CORRIGÉ : Traite le fichier Excel des emails.
     */
    public function storeEmails(Request $request)
    {
        // Validation spécifique pour le formulaire des emails
        $request->validate(['file-emails' => 'required|mimes:xlsx,xls,csv|max:20480']);

        $file = $request->file('file-emails');
        $importedCount = 0;
        $skippedRows = [];

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

            DB::transaction(function () use ($worksheet, $header, &$importedCount, &$skippedRows) {
                foreach ($worksheet->getRowIterator(2) as $rowIndex => $row) {
                    $rowData = [];
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getValue();
                    }
                    $rowAssoc = array_combine($header, array_pad($rowData, count($header), null));

                    $entident = $rowAssoc['entident'] ?? null;
                    $emailAddress = $rowAssoc['email'] ?? null;

                    if (empty($entident) || empty($emailAddress)) {
                        $skippedRows[] = $rowIndex;
                        continue;
                    }

                    $entreprise = Entreprise::find($entident);

                    if ($entreprise) {
                        $entreprise->emails()->updateOrCreate(
                            ['email' => $emailAddress],
                            ['source' => $rowAssoc['source'] ?? 'import_excel']
                        );
                        $importedCount++;
                    } else {
                        $skippedRows[] = $rowIndex;
                    }
                }
            });
        } catch (Throwable $e) {
            return redirect()->back()->with('error_emails', 'Erreur durant l\'importation des emails. Erreur : ' . $e->getMessage());
        }

        $message = "Importation des emails réussie ! **{$importedCount} adresses email** ont été ajoutées ou mises à jour.";
        if (!empty($skippedRows)) {
            $skippedCount = count($skippedRows);
            $message .= " Attention : **{$skippedCount} lignes** ont été ignorées (entreprise non trouvée ou données manquantes).";
        }

        return redirect()->route('admin.entreprises.import.form')->with('success_emails', $message);
    }
}
