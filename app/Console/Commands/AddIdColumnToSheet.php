<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;

class AddIdColumnToSheet extends Command
{
    protected $signature = 'sheets:add-id-column';
    protected $description = 'Add ID column to the Properties Google Sheet and populate it';

    protected $sheetsService;

    public function __construct(PropertyGoogleSheetsService $sheetsService)
    {
        parent::__construct();
        $this->sheetsService = $sheetsService;
    }

    public function handle()
    {
        $this->info('Adding ID column to Google Sheet and populating it...');
        
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
            
            // Get all data
            $range = $sheetName . '!A:ZZ';
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();
            
            if (empty($rows)) {
                $this->error('No data in sheet');
                return 1;
            }
            
            $headers = $rows[0];
            
            // Check if ID column already exists
            if (in_array('id', array_map('strtolower', $headers))) {
                $this->warn('ID column already exists!');
                return 0;
            }
            
            // Insert 'id' as the first column
            array_unshift($headers, 'id');
            
            // Update header row
            $lastColumn = $this->columnIndexToLetter(count($headers) - 1);
            $headerRange = $sheetName . '!A1:' . $lastColumn . '1';
            
            $valueRange = new \Google\Service\Sheets\ValueRange();
            $valueRange->setRange($headerRange);
            $valueRange->setValues([$headers]);
            
            $service->spreadsheets_values->update(
                $spreadsheetId,
                $headerRange,
                $valueRange,
                ['valueInputOption' => 'USER_ENTERED']
            );
            
            $this->info('✓ Added ID column as first column');
            
            // Now populate IDs for all existing rows
            $dataRows = array_slice($rows, 1); // Skip header
            $idUpdates = [];
            
            $this->info('Generating IDs for ' . count($dataRows) . ' properties...');
            $bar = $this->output->createProgressBar(count($dataRows));
            
            foreach ($dataRows as $index => $row) {
                $rowNumber = $index + 2; // +2 because: +1 for 0-based index, +1 for header row
                
                // Generate ID based on row content (matching the getAllProperties logic)
                $rowHash = md5(json_encode($row));
                $id = 'sheet_' . $rowHash;
                
                $cellRange = 'A' . $rowNumber;
                $fullRange = $sheetName . '!' . $cellRange;
                
                $valueRange = new \Google\Service\Sheets\ValueRange();
                $valueRange->setRange($fullRange);
                $valueRange->setValues([[$id]]);
                
                $idUpdates[] = $valueRange;
                
                $bar->advance();
                
                // Batch update every 100 rows to avoid hitting API limits
                if (count($idUpdates) >= 100) {
                    $batchUpdateRequest = new \Google\Service\Sheets\BatchUpdateValuesRequest();
                    $batchUpdateRequest->setValueInputOption('USER_ENTERED');
                    $batchUpdateRequest->setData($idUpdates);
                    
                    $service->spreadsheets_values->batchUpdate(
                        $spreadsheetId,
                        $batchUpdateRequest
                    );
                    
                    $idUpdates = [];
                    usleep(100000); // 100ms delay to avoid rate limiting
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
            
            $this->info('✓ Successfully populated IDs for all properties!');
            
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

