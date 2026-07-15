<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;
use App\Services\ScrapedListingsApiService;

class ClearPropertiesCache extends Command
{
    protected $signature = 'properties:clear-cache';
    protected $description = 'Clear the property feed caches (Harbor Ops API and Google Sheets)';

    public function handle(PropertyGoogleSheetsService $sheetsService, ScrapedListingsApiService $apiService)
    {
        $apiService->clearCache();
        $sheetsService->clearCache();
        $this->info('Properties cache cleared successfully!');
        return 0;
    }
}
