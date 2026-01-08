<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;

class DebugSheetData extends Command
{
    protected $signature = 'sheets:debug-data';
    protected $description = 'Debug Google Sheet data structure';

    protected $sheetsService;

    public function __construct(PropertyGoogleSheetsService $sheetsService)
    {
        parent::__construct();
        $this->sheetsService = $sheetsService;
    }

    public function handle()
    {
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
            
            // Get raw data from sheet
            $range = $sheetName . '!A1:F2';  // First row (headers) and second row (first data row)
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $rows = $response->getValues();
            
            $this->info('=== RAW DATA FROM GOOGLE SHEET ===');
            $this->newLine();
            
            if (isset($rows[0])) {
                $this->info('HEADERS (Row 1):');
                foreach ($rows[0] as $index => $header) {
                    $column = $this->columnIndexToLetter($index);
                    $this->line("  Column {$column} (index {$index}): {$header}");
                }
            }
            
            $this->newLine();
            
            if (isset($rows[1])) {
                $this->info('FIRST DATA ROW (Row 2):');
                foreach ($rows[1] as $index => $value) {
                    $column = $this->columnIndexToLetter($index);
                    $header = $rows[0][$index] ?? 'Unknown';
                    $this->line("  Column {$column} ({$header}): " . substr($value, 0, 50));
                }
            }
            
            $this->newLine();
            $this->info('=== MAPPED PROPERTY DATA ===');
            $this->newLine();
            
            // Now check what the service returns
            $this->sheetsService->clearCache();
            $properties = $this->sheetsService->getAllProperties();
            $firstProperty = $properties->first();
            
            if ($firstProperty) {
                foreach ($firstProperty as $key => $value) {
                    if (in_array($key, ['id', 'title', 'agent_name', 'location', 'price'])) {
                        $displayValue = is_string($value) ? substr($value, 0, 50) : $value;
                        $this->line("  {$key}: {$displayValue}");
                    }
                }
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Failed: ' . $e->getMessage());
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

