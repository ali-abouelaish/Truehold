<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;

class AddFlagColumnsToSheet extends Command
{
    protected $signature = 'sheets:add-flag-columns';
    protected $description = 'Add flag and flag_color columns to the Properties Google Sheet';

    protected $sheetsService;

    public function __construct(PropertyGoogleSheetsService $sheetsService)
    {
        parent::__construct();
        $this->sheetsService = $sheetsService;
    }

    public function handle()
    {
        $this->info('Adding flag and flag_color columns to Google Sheet...');
        
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
            
            // Get current headers
            $range = $sheetName . '!1:1';
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $headers = $response->getValues()[0] ?? [];
            
            $this->info('Current columns: ' . count($headers));
            $this->info('Last column: ' . end($headers));
            
            // Check if columns already exist
            if (in_array('flag', $headers) && in_array('flag_color', $headers)) {
                $this->warn('Columns already exist!');
                return 0;
            }
            
            // Add new columns
            $headers[] = 'flag';
            $headers[] = 'flag_color';
            
            // Convert column count to letter
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
            
            $this->info('✓ Successfully added columns!');
            $this->info('Total columns now: ' . count($headers));
            $this->info('New columns: flag (column ' . ($this->columnIndexToLetter(count($headers) - 2)) . '), flag_color (column ' . $lastColumn . ')');
            
            // Clear cache
            $this->sheetsService->clearCache();
            $this->info('✓ Cache cleared');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Failed to add columns: ' . $e->getMessage());
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

