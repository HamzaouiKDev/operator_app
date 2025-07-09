<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EchantillonEnquete;
use App\Models\Enquete;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EchantillonImportController extends Controller
{
    /**
     * Affiche le formulaire d'importation des échantillons.
     */
    public function create()
    {
        $enquetes = Enquete::whereIn('statut', ['brouillon', 'active'])->get();
        return view('admin.echantillons.import', compact('enquetes'));
    }

    /**
     * Traite l'importation du fichier Excel des échantillons pour une enquête spécifique.
     */
    public function store(Request $request)
    {
        set_time_limit(300); // 5 minutes

        $request->validate([
            'enquete_id' => 'required|exists:enquetes,id',
            'echantillon_file' => 'required|mimes:xlsx,xls,csv|max:20480',
        ]);

        $enqueteId = $request->input('enquete_id');
        $file = $request->file('echantillon_file');
        
        $dataChunk = [];
        $chunkSize = 1000;
        $totalImported = 0;
        $totalSkipped = 0;
        $totalDuplicates = 0;

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();

            $headerCell = $worksheet->getCell('A1')->getValue();
            if (strtolower(trim($headerCell)) !== 'entident') {
                throw new \Exception("L'en-tête de la colonne A doit être 'entident'.");
            }
            
            foreach ($worksheet->getRowIterator(2) as $row) {
                $entrepriseIdFromExcel = trim($row->getCellIterator()->current()->getValue());
                
                if (!empty($entrepriseIdFromExcel)) {
                    $dataChunk[] = $entrepriseIdFromExcel;
                }

                if (count($dataChunk) >= $chunkSize) {
                    list($imported, $skipped, $duplicates) = $this->processAndInsertEchantillons($dataChunk, $enqueteId);
                    $totalImported += $imported;
                    $totalSkipped += $skipped;
                    $totalDuplicates += $duplicates;
                    $dataChunk = []; 
                }
            }

            if (!empty($dataChunk)) {
                list($imported, $skipped, $duplicates) = $this->processAndInsertEchantillons($dataChunk, $enqueteId);
                $totalImported += $imported;
                $totalSkipped += $skipped;
                $totalDuplicates += $duplicates;
            }

        } catch (Throwable $e) {
            Log::error("Erreur d'importation de l'échantillon : " . $e->getMessage());
            return redirect()->back()->with('error', "Une erreur est survenue : " . $e->getMessage());
        }

        $message = "Importation terminée !<br>";
        $message .= "<strong>{$totalImported}</strong> entreprises ajoutées à l'échantillon.<br>";
        if ($totalDuplicates > 0) $message .= "<strong>{$totalDuplicates}</strong> entreprises étaient déjà dans l'échantillon.<br>";
        if ($totalSkipped > 0) $message .= "<strong>{$totalSkipped}</strong> ID d'entreprises invalides ou lignes vides ont été ignorées.<br>";

        return redirect()->route('admin.echantillons.import.form')->with('success', $message);
    }

    /**
     * Fonction privée pour traiter et insérer un lot de données d'échantillons.
     */
    private function processAndInsertEchantillons(array $chunkData, int $enqueteId)
    {
        $entrepriseIdsFromFile = array_unique(array_filter($chunkData));

        if (empty($entrepriseIdsFromFile)) {
            return [0, count($chunkData), 0];
        }
        
        $existingEntrepriseIds = Entreprise::whereIn('id', $entrepriseIdsFromFile)->pluck('id')->flip();
        
        $existingEchantillons = EchantillonEnquete::where('enquete_id', $enqueteId)
            ->whereIn('entreprise_id', $existingEntrepriseIds->keys())
            ->pluck('entreprise_id')
            ->flip();

        $dataToInsert = [];
        $skippedCount = 0;
        $duplicateCount = 0;
        
        // CORRECTION: Utiliser le format de date non ambigu 'Ymd H:i:s' pour SQL Server
        $now = now()->format('Ymd H:i:s');

        foreach ($entrepriseIdsFromFile as $entrepriseId) {
            if (!$existingEntrepriseIds->has($entrepriseId)) {
                $skippedCount++;
                continue;
            }
            if ($existingEchantillons->has($entrepriseId)) {
                $duplicateCount++;
                continue;
            }

            $dataToInsert[] = [
                'entreprise_id' => $entrepriseId,
                'enquete_id'    => $enqueteId,
                'statut'        => 'non traite',
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }
        
        if (!empty($dataToInsert)) {
            foreach(array_chunk($dataToInsert, 200) as $insertChunk) {
                EchantillonEnquete::insert($insertChunk);
            }
        }

        return [count($dataToInsert), $skippedCount, $duplicateCount];
    }
}
