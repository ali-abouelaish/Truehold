<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;
use Illuminate\Support\Facades\Log;

class TestPropertiesGoogleSheets extends Command
{
    protected $signature = 'properties:test-sheets';
    protected $description = 'Test Google Sheets properties integration';

    public function handle(PropertyGoogleSheetsService $service)
    {
        $this->info('Testing Google Sheets Properties Integration...');
        $this->newLine();

        // Check configuration
        $spreadsheetId = config('services.google.properties.spreadsheet_id');
        $sheetName = config('services.google.properties.sheet_name', 'Properties');
        
        $this->info("Spreadsheet ID: " . ($spreadsheetId ?: 'NOT CONFIGURED'));
        $this->info("Sheet Name: " . $sheetName);
        $this->newLine();

        if (!$spreadsheetId) {
            $this->error('GOOGLE_PROPERTIES_SPREADSHEET_ID is not configured in .env');
            return 1;
        }

        try {
            // Clear cache first
            $service->clearCache();
            $this->info('Cache cleared');
            
            // Try to fetch properties
            $this->info('Fetching properties from Google Sheets...');
            $properties = $service->getAllProperties();
            
            $this->newLine();
            $this->info("Total properties found: " . $properties->count());
            
            if ($properties->count() > 0) {
                $this->info('Sample property (first one):');
                $first = $properties->first();
                $this->line('  ID: ' . ($first['id'] ?? 'N/A'));
                $this->line('  Title: ' . ($first['title'] ?? 'N/A'));
                $this->line('  Location: ' . ($first['location'] ?? 'N/A'));
                $this->line('  Price: ' . ($first['price'] ?? 'N/A'));
                
                $this->newLine();
                $this->info('All property IDs:');
                $properties->take(10)->each(function ($prop) {
                    $this->line('  - ' . ($prop['id'] ?? 'NO ID'));
                });
                
                if ($properties->count() > 10) {
                    $this->line('  ... and ' . ($properties->count() - 10) . ' more');
                }
            } else {
                $this->warn('No properties found. Checking logs for details...');
                $this->newLine();
                $this->info('Recent log entries:');
                $this->line('Check storage/logs/laravel.log for detailed error messages');
            }
            
            // Test filter values
            $this->newLine();
            $this->info('Testing filter values...');
            $filterValues = $service->getFilterValues();
            $this->line('  Locations: ' . $filterValues['locations']->count());
            $this->line('  Property Types: ' . $filterValues['propertyTypes']->count());
            $this->line('  Available Dates: ' . $filterValues['available_dates']->count());
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
