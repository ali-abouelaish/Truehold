<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PropertyGoogleSheetsService;

class TestFlagUpdate extends Command
{
    protected $signature = 'sheets:test-flag-update {propertyId}';
    protected $description = 'Test updating a property flag';

    protected $sheetsService;

    public function __construct(PropertyGoogleSheetsService $sheetsService)
    {
        parent::__construct();
        $this->sheetsService = $sheetsService;
    }

    public function handle()
    {
        $propertyId = $this->argument('propertyId');
        
        $this->info("Testing flag update for property: {$propertyId}");
        
        $result = $this->sheetsService->updateProperty($propertyId, [
            'flag' => 'Premium',
            'flag_color' => 'linear-gradient(135deg, #d4af37, #b8941f)'
        ]);
        
        if ($result) {
            $this->info('✓ Update successful!');
        } else {
            $this->error('✗ Update failed! Check logs for details.');
        }
        
        return $result ? 0 : 1;
    }
}

