<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;

class ClearPropertiesCache extends Command
{
    protected $signature = 'properties:clear-cache';
    protected $description = 'Clear the Google Sheets properties cache';

    public function handle(PropertyGoogleSheetsService $service)
    {
        $service->clearCache();
        $this->info('Properties cache cleared successfully!');
        return 0;
    }
}
