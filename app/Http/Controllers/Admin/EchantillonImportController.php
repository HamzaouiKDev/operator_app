<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquete;
use App\Models\Entreprise;
use App\Models\EchantillonEnquete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EchantillonImportController extends Controller
{
    /**
     * Affiche le formulaire d'importation de l'échantillon.
     */
    public function create()
    {
        $enquetes = Enquete::whereIn('statut', ['brouillon', 'active'])->get();
        return view('admin.echantillons.import', compact('enquetes'));
    }

    /**
     * Traite le fichier Excel pour importer l'échantillon.
     */
    public function store(Request $request)
    {
        $request->validate([
            'enquete_id' => 'required|exists:enquetes,id',
            'echantillon_file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $enqueteId = $request->input('enquete_id');
        $file = $request->file('echantillon_file');

        $importedCount = 0;
        $skippedCount = 0;
        $duplicateCount = 0;
        $invalidIdCount = 0;

        try {
            DB::transaction(function () use ($file, $enqueteId, &$importedCount, &$skippedCount, &$duplicateCount, &$invalidIdCount) {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                
                $header = strtolower(trim($worksheet->getCell('A1')->getValue()));
                if ($header !== 'entident') {
                    throw new \Exception("L'en-tête de la colonne A doit être 'entident'.");
                }
                
                foreach ($worksheet->getRowIterator(2) as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cell = $cellIterator->current();
                    
                    // On récupère l'identifiant de l'entreprise depuis le fichier
                    $entrepriseIdFromExcel = trim($cell->getValue());

                    if (empty($entrepriseIdFromExcel)) {
                        $skippedCount++;
                        continue;
                    }
                    
                    // CORRECTION : On cherche l'entreprise directement par son ID
                    $entreprise = Entreprise::find($entrepriseIdFromExcel);

                    if (!$entreprise) {
                        $invalidIdCount++;
                        Log::warning("ID d'entreprise non trouvé : '{$entrepriseIdFromExcel}'");
                        continue;
                    }

                    $isDuplicate = EchantillonEnquete::where('enquete_id', $enqueteId)
                                                     ->where('entreprise_id', $entreprise->id)
                                                     ->exists();

                    if ($isDuplicate) {
                        $duplicateCount++;
                        continue;
                    }

                    EchantillonEnquete::create([
                        'enquete_id' => $enqueteId,
                        'entreprise_id' => $entreprise->id,
                        'statut' => 'pas traite',
                    ]);
                    $importedCount++;
                }
            });
        } catch (\Exception $e) {
            Log::error("Erreur d'importation de l'échantillon : " . $e->getMessage());
            return redirect()->back()->with('error', "Une erreur est survenue : " . $e->getMessage());
        }

        $message = "Importation terminée !<br>";
        $message .= "<strong>{$importedCount}</strong> entreprises ajoutées à l'échantillon.<br>";
        if ($duplicateCount > 0) $message .= "<strong>{$duplicateCount}</strong> entreprises étaient déjà dans l'échantillon.<br>";
        if ($invalidIdCount > 0) $message .= "<strong>{$invalidIdCount}</strong> ID d'entreprises n'ont pas été trouvés.<br>";
        if ($skippedCount > 0) $message .= "<strong>{$skippedCount}</strong> lignes vides ont été ignorées.";

        return redirect()->route('admin.echantillons.import.form')->with('success', $message);
    }
}
