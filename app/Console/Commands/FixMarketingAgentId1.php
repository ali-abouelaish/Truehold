<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FixMarketingAgentId1 extends Command
{
    protected $signature = 'fix:marketing-agent-id-1 {--dry-run : Show what would be updated without making changes}';
    protected $description = 'Fix marketing agent ID 1 by mapping to an existing agent or creating a new one';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🚀 Fixing marketing agent ID 1...');

        // Get rental codes with marketing_agent = '1'
        $rentalCodes = RentalCode::where('marketing_agent', '1')->get();
        $this->info("📋 Found {$rentalCodes->count()} rental codes with marketing_agent = '1'");

        if ($rentalCodes->isEmpty()) {
            $this->info('✅ No rental codes found with marketing_agent = "1"');
            return;
        }

        // Get the first available agent to map to
        $targetAgent = User::where('role', 'agent')->first();
        
        if (!$targetAgent) {
            $this->error('❌ No agent users found in the system');
            return;
        }

        $this->info("🎯 Will map marketing_agent '1' to agent: {$targetAgent->name} (ID: {$targetAgent->id})");

        $updatedCount = 0;

        DB::transaction(function () use ($rentalCodes, $targetAgent, $isDryRun, &$updatedCount) {
            foreach ($rentalCodes as $code) {
                if (!$isDryRun) {
                    $code->marketing_agent_id = $targetAgent->id;
                    $code->save();
                }
                $updatedCount++;
                $this->line("✅ Updated rental code {$code->rental_code} → Marketing Agent: {$targetAgent->name} (ID: {$targetAgent->id})");
            }
        });

        // Display summary
        $this->newLine();
        $this->info('📊 Update Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Rental Codes Updated', $updatedCount],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->info('🔍 This was a dry run. Run the command without --dry-run to apply changes.');
        } else {
            $this->newLine();
            $this->info('✅ Marketing agent ID 1 fix completed successfully!');
        }
    }
}