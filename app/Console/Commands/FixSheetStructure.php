<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;

class FixSheetStructure extends Command
{
    protected $signature = 'sheets:fix-structure';
    protected $description = 'Fix Google Sheet structure after ID column was added incorrectly';

    protected $sheetsService;

    public function __construct(PropertyGoogleSheetsService $sheetsService)
    {
        parent::__construct();
        $this->sheetsService = $sheetsService;
    }

    public function handle()
    {
        $this->warn('⚠️  This will revert the ID column changes and restore original structure');
        if (!$this->confirm('Do you want to proceed?')) {
            return 0;
        }
        
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
            $this->info('Current headers: ' . implode(', ', array_slice($headers, 0, 10)));
            $this->info('Total columns: ' . count($headers));
            
            // Check if 'id' is the last column
            $idIndex = array_search('id', $headers);
            if ($idIndex === false) {
                $this->warn('No ID column found');
            } else {
                $this->info('ID column is at position: ' . $idIndex);
            }
            
            // Remove 'id', 'flag', and 'flag_color' columns
            $columnsToRemove = ['id', 'flag', 'flag_color'];
            $this->info('Removing columns: ' . implode(', ', $columnsToRemove));
            
            // Find indices of columns to remove
            $removeIndices = [];
            foreach ($columnsToRemove as $col) {
                $index = array_search($col, $headers);
                if ($index !== false) {
                    $removeIndices[] = $index;
                }
            }
            
            // Sort in reverse order so we can remove from end to start
            rsort($removeIndices);
            
            // Remove from headers
            foreach ($removeIndices as $index) {
                unset($headers[$index]);
            }
            $headers = array_values($headers); // Re-index
            
            // Now add columns at the end in correct order
            $headers[] = 'flag';
            $headers[] = 'flag_color';
            
            $this->info('New header structure: ' . implode(', ', $headers));
            
            // Remove columns from all data rows
            $newRows = [$headers];
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Remove columns
                foreach ($removeIndices as $index) {
                    if (isset($row[$index])) {
                        unset($row[$index]);
                    }
                }
                $row = array_values($row); // Re-index
                
                // Pad to match header length
                while (count($row) < count($headers)) {
                    $row[] = '';
                }
                
                $newRows[] = $row;
            }
            
            $this->info('Processing ' . (count($newRows) - 1) . ' data rows...');
            
            // Clear entire sheet
            $clearRange = $sheetName . '!A1:ZZ' . count($rows);
            $service->spreadsheets_values->clear(
                $spreadsheetId,
                $clearRange,
                new \Google\Service\Sheets\ClearValuesRequest()
            );
            
            $this->info('✓ Cleared sheet');
            
            // Write new data
            $lastColumn = $this->columnIndexToLetter(count($headers) - 1);
            $updateRange = $sheetName . '!A1:' . $lastColumn . count($newRows);
            
            $valueRange = new \Google\Service\Sheets\ValueRange();
            $valueRange->setRange($updateRange);
            $valueRange->setValues($newRows);
            
            $service->spreadsheets_values->update(
                $spreadsheetId,
                $updateRange,
                $valueRange,
                ['valueInputOption' => 'USER_ENTERED']
            );
            
            $this->info('✓ Wrote restored data');
            $this->info('New structure: ' . count($headers) . ' columns, ' . (count($newRows) - 1) . ' data rows');
            
            // Clear cache
            $this->sheetsService->clearCache();
            $this->info('✓ Cache cleared');
            
            $this->info('');
            $this->info('🎉 Sheet structure fixed!');
            $this->info('The sheet now has the original structure with flag and flag_color columns added at the end.');
            $this->info('IDs will be generated dynamically based on URL/title as before.');
            
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

