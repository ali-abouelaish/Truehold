<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalCode;
use App\Models\User;

class VerifyForeignKeys extends Command
{
    protected $signature = 'verify:foreign-keys';
    protected $description = 'Verify that all rental codes have proper foreign keys';

    public function handle()
    {
        $this->info('🔍 Verifying foreign keys in rental codes...');

        // Get all rental codes
        $totalRentalCodes = RentalCode::count();
        $this->info("📊 Total rental codes: {$totalRentalCodes}");

        // Check rental agent foreign keys
        $rentalAgentWithKeys = RentalCode::whereNotNull('rental_agent_id')->count();
        $rentalAgentWithoutKeys = RentalCode::whereNull('rental_agent_id')->whereNotNull('agent_name')->count();
        
        $this->info("🏠 Rental agents with foreign keys: {$rentalAgentWithKeys}");
        $this->info("🏠 Rental agents without foreign keys: {$rentalAgentWithoutKeys}");

        // Check marketing agent foreign keys
        $marketingAgentWithKeys = RentalCode::whereNotNull('marketing_agent_id')->count();
        $marketingAgentWithoutKeys = RentalCode::whereNull('marketing_agent_id')->whereNotNull('marketing_agent')->count();
        
        $this->info("📢 Marketing agents with foreign keys: {$marketingAgentWithKeys}");
        $this->info("📢 Marketing agents without foreign keys: {$marketingAgentWithoutKeys}");

        // Check for invalid foreign keys
        $invalidRentalAgentKeys = RentalCode::whereNotNull('rental_agent_id')
            ->whereNotIn('rental_agent_id', User::where('role', 'agent')->pluck('id'))
            ->count();
            
        $invalidMarketingAgentKeys = RentalCode::whereNotNull('marketing_agent_id')
            ->whereNotIn('marketing_agent_id', User::where('role', 'agent')->pluck('id'))
            ->count();

        $this->info("❌ Invalid rental agent foreign keys: {$invalidRentalAgentKeys}");
        $this->info("❌ Invalid marketing agent foreign keys: {$invalidMarketingAgentKeys}");

        // Display summary
        $this->newLine();
        $this->info('📊 Foreign Key Summary:');
        $this->table(
            ['Metric', 'Count', 'Status'],
            [
                ['Total Rental Codes', $totalRentalCodes, '📊'],
                ['Rental Agents with Keys', $rentalAgentWithKeys, $rentalAgentWithKeys > 0 ? '✅' : '❌'],
                ['Rental Agents without Keys', $rentalAgentWithoutKeys, $rentalAgentWithoutKeys === 0 ? '✅' : '⚠️'],
                ['Marketing Agents with Keys', $marketingAgentWithKeys, $marketingAgentWithKeys > 0 ? '✅' : '❌'],
                ['Marketing Agents without Keys', $marketingAgentWithoutKeys, $marketingAgentWithoutKeys === 0 ? '✅' : '⚠️'],
                ['Invalid Rental Agent Keys', $invalidRentalAgentKeys, $invalidRentalAgentKeys === 0 ? '✅' : '❌'],
                ['Invalid Marketing Agent Keys', $invalidMarketingAgentKeys, $invalidMarketingAgentKeys === 0 ? '✅' : '❌'],
            ]
        );

        if ($rentalAgentWithoutKeys === 0 && $marketingAgentWithoutKeys === 0 && $invalidRentalAgentKeys === 0 && $invalidMarketingAgentKeys === 0) {
            $this->newLine();
            $this->info('🎉 All foreign keys are properly populated and valid!');
        } else {
            $this->newLine();
            $this->warn('⚠️  Some foreign keys need attention.');
        }
    }
}