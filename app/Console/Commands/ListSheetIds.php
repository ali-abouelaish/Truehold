<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;

class ListSheetIds extends Command
{
    protected $signature = 'sheets:list-ids {search?}';
    protected $description = 'List all property IDs from Google Sheet';

    protected $sheetsService;

    public function __construct(PropertyGoogleSheetsService $sheetsService)
    {
        parent::__construct();
        $this->sheetsService = $sheetsService;
    }

    public function handle()
    {
        $search = $this->argument('search');
        
        // Clear cache first
        $this->sheetsService->clearCache();
        
        $properties = $this->sheetsService->getAllProperties();
        
        $this->info('Found ' . $properties->count() . ' properties');
        
        if ($search) {
            $this->info("Searching for IDs containing: {$search}");
            $properties = $properties->filter(function($prop) use ($search) {
                return str_contains($prop['id'] ?? '', $search);
            });
            $this->info('Matching properties: ' . $properties->count());
        }
        
        $this->table(
            ['ID', 'Title', 'Agent'],
            $properties->take(20)->map(function($prop) {
                return [
                    $prop['id'] ?? 'N/A',
                    substr($prop['title'] ?? 'N/A', 0, 50),
                    $prop['agent_name'] ?? 'N/A'
                ];
            })
        );
        
        return 0;
    }
}

