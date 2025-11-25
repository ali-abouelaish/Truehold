<?php

namespace App\Console\Commands;

use App\Models\RentalCode;
use App\Services\GoogleSheetsService;
use Illuminate\Console\Command;

class SyncRentalCodeToSheets extends Command
{
    protected $signature = 'rental-code:sync-sheets {id : The rental code ID to sync}';
    protected $description = 'Sync a rental code to Google Sheets';

    public function handle()
    {
        $id = $this->argument('id');
        
        $rentalCode = RentalCode::find($id);
        
        if (!$rentalCode) {
            $this->error("Rental code with ID {$id} not found.");
            return 1;
        }
        
        $this->info("Found rental code: {$rentalCode->rental_code}");
        $this->info("Attempting to sync to Google Sheets...");
        
        try {
            $service = new GoogleSheetsService();
            $result = $service->appendRentalCode($rentalCode);
            
            if ($result) {
                $this->info("✅ Successfully synced to Google Sheets!");
                return 0;
            } else {
                $this->error("❌ Failed to sync. Check logs for details.");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}

