<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;

class FixIdColumnPosition extends Command
{
    protected $signature = 'sheets:fix-id-column';
    protected $description = 'Move ID column to the end and repopulate correctly';

    protected $sheetsService;

    public function __construct(PropertyGoogleSheetsService $sheetsService)
    {
        parent::__construct();
        $this->sheetsService = $sheetsService;
    }

    public function handle()
    {
        $this->info('Fixing ID column position...');
        
        try {
            // Access protected properties via reflection
            $reflection = new \ReflectionClass($this->sheetsService);
            
            $spreadsheetIdProperty = $reflection->getProperty('spreadsheetId');
            $spreadsheetIdProperty->setAccessible(true);
            $spreadsheetId = $spreadsheetIdProperty->getValue($this->sheetsService);
            
            $sheetNameProperty = $reflection->getProperty('sheetName');
            $sheetNameProperty->setAccessible(true);
            $sheetName = $sheetNameProperty->getValue($this->sheetsService);
            
            $serviceProperty = $reflection->getProperty('service');
            $serviceProperty->setAccessible(true);
            $service = $serviceProperty->getValue($this->sheetsService);
            
            if (!$service) {
                $this->error('Google Sheets service not initialized');
                return 1;
            }
            
            // Step 1: Get all current data
            $range = $sheetName . '!A:ZZ';
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();
            
            if (empty($rows)) {
                $this->error('No data in sheet');
                return 1;
            }
            
            $this->info('Current total columns: ' . count($rows[0]));
            
            // Step 2: Remove the first column (id) from each row
            $fixedRows = [];
            foreach ($rows as $index => $row) {
                $fixedRow = array_slice($row, 1); // Remove first column
                $fixedRows[] = $fixedRow;
            }
            
            $this->info('Step 1: Removed ID column from all rows');
            
            // Step 3: Add 'id' header to the end
            $fixedRows[0][] = 'id';
            
            // Step 4: Update the sheet with fixed data
            $lastColumn = $this->columnIndexToLetter(count($fixedRows[0]) - 1);
            $updateRange = $sheetName . '!A1:' . $lastColumn . count($fixedRows);
            
            $valueRange = new \Google\Service\Sheets\ValueRange();
            $valueRange->setRange($updateRange);
            $valueRange->setValues($fixedRows);
            
            $service->spreadsheets_values->update(
                $spreadsheetId,
                $updateRange,
                $valueRange,
                ['valueInputOption' => 'USER_ENTERED']
            );
            
            $this->info('Step 2: Updated sheet with corrected data');
            
            // Step 5: Now populate IDs in the last column
            $this->info('Step 3: Populating IDs...');
            $bar = $this->output->createProgressBar(count($fixedRows) - 1);
            
            $idUpdates = [];
            $idColumnLetter = $this->columnIndexToLetter(count($fixedRows[0]) - 1);
            
            for ($i = 1; $i < count($fixedRows); $i++) {
                $rowNumber = $i + 1;
                $row = $fixedRows[$i];
                
                // Generate ID based on row content (matching the getAllProperties logic)
                // Pad row to ensure it has enough columns
                while (count($row) < count($fixedRows[0]) - 1) {
                    $row[] = '';
                }
                
                $rowHash = md5(json_encode($row));
                $id = 'sheet_' . $rowHash;
                
                $cellRange = $idColumnLetter . $rowNumber;
                $fullRange = $sheetName . '!' . $cellRange;
                
                $valueRange = new \Google\Service\Sheets\ValueRange();
                $valueRange->setRange($fullRange);
                $valueRange->setValues([[$id]]);
                
                $idUpdates[] = $valueRange;
                
                $bar->advance();
                
                // Batch update every 100 rows
                if (count($idUpdates) >= 100) {
                    $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateValuesRequest();
                    $batchUpdateRequest->setValueInputOption('USER_ENTERED');
                    $batchUpdateRequest->setData($idUpdates);
                    
                    $service->spreadsheets_values->batchUpdate(
                        $spreadsheetId,
                        $batchUpdateRequest
                    );
                    
                    $idUpdates = [];
                    usleep(100000);
                }
            }
            
            // Update remaining IDs
            if (!empty($idUpdates)) {
                $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateValuesRequest();
                $batchUpdateRequest->setValueInputOption('USER_ENTERED');
                $batchUpdateRequest->setData($idUpdates);
                
                $service->spreadsheets_values->batchUpdate(
                    $spreadsheetId,
                    $batchUpdateRequest
                );
            }
            
            $bar->finish();
            $this->newLine();
            
            $this->info('✓ ID column moved to end and repopulated!');
            $this->info('Total columns now: ' . count($fixedRows[0]));
            
            // Clear cache
            $this->sheetsService->clearCache();
            $this->info('✓ Cache cleared');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
    
    private function columnIndexToLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intval($index / 26) - 1;
        }
        return $letter;
    }
}

