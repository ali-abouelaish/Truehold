<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalCode;
use Illuminate\Support\Facades\DB;

class VerifyColumnRemoval extends Command
{
    protected $signature = 'verify:column-removal';
    protected $description = 'Verify that old agent name columns have been removed';

    public function handle()
    {
        $this->info('🔍 Verifying that old agent name columns have been removed...');

        try {
            // Try to query the old columns - this should fail if they're removed
            $hasAgentName = DB::select("SHOW COLUMNS FROM rental_codes LIKE 'agent_name'");
            $hasMarketingAgent = DB::select("SHOW COLUMNS FROM rental_codes LIKE 'marketing_agent'");
            
            if (empty($hasAgentName) && empty($hasMarketingAgent)) {
                $this->info('✅ Old columns (agent_name, marketing_agent) have been successfully removed');
            } else {
                $this->warn('⚠️  Old columns still exist:');
                if (!empty($hasAgentName)) {
                    $this->warn('   - agent_name column still exists');
                }
                if (!empty($hasMarketingAgent)) {
                    $this->warn('   - marketing_agent column still exists');
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ Error checking columns: ' . $e->getMessage());
        }

        // Check that foreign key columns exist
        try {
            $hasRentalAgentId = DB::select("SHOW COLUMNS FROM rental_codes LIKE 'rental_agent_id'");
            $hasMarketingAgentId = DB::select("SHOW COLUMNS FROM rental_codes LIKE 'marketing_agent_id'");
            
            if (!empty($hasRentalAgentId) && !empty($hasMarketingAgentId)) {
                $this->info('✅ Foreign key columns (rental_agent_id, marketing_agent_id) are present');
            } else {
                $this->error('❌ Foreign key columns are missing:');
                if (empty($hasRentalAgentId)) {
                    $this->error('   - rental_agent_id column is missing');
                }
                if (empty($hasMarketingAgentId)) {
                    $this->error('   - marketing_agent_id column is missing');
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ Error checking foreign key columns: ' . $e->getMessage());
        }

        // Test that rental codes can still be queried
        try {
            $rentalCodesCount = RentalCode::count();
            $this->info("✅ Rental codes can still be queried (count: {$rentalCodesCount})");
        } catch (\Exception $e) {
            $this->error('❌ Error querying rental codes: ' . $e->getMessage());
        }

        // Test foreign key relationships
        try {
            $rentalCodesWithRentalAgent = RentalCode::whereNotNull('rental_agent_id')->count();
            $rentalCodesWithMarketingAgent = RentalCode::whereNotNull('marketing_agent_id')->count();
            
            $this->info("✅ Foreign key relationships working:");
            $this->info("   - Rental codes with rental_agent_id: {$rentalCodesWithRentalAgent}");
            $this->info("   - Rental codes with marketing_agent_id: {$rentalCodesWithMarketingAgent}");
        } catch (\Exception $e) {
            $this->error('❌ Error testing foreign key relationships: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('🎉 Column removal verification completed!');
    }
}