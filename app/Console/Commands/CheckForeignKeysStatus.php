<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckForeignKeysStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:foreign-keys-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of foreign key columns in rental codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking foreign key migration status...');

        // Check if old columns exist
        $hasAgentName = Schema::hasColumn('rental_codes', 'agent_name');
        $hasMarketingAgent = Schema::hasColumn('rental_codes', 'marketing_agent');
        
        // Check if new columns exist
        $hasRentalAgentId = Schema::hasColumn('rental_codes', 'rental_agent_id');
        $hasMarketingAgentId = Schema::hasColumn('rental_codes', 'marketing_agent_id');

        $this->newLine();
        $this->info('📊 Database Schema Status:');
        $this->table(
            ['Column', 'Exists'],
            [
                ['agent_name (old)', $hasAgentName ? '✅ Yes' : '❌ No'],
                ['marketing_agent (old)', $hasMarketingAgent ? '✅ Yes' : '❌ No'],
                ['rental_agent_id (new)', $hasRentalAgentId ? '✅ Yes' : '❌ No'],
                ['marketing_agent_id (new)', $hasMarketingAgentId ? '✅ Yes' : '❌ No'],
            ]
        );

        if ($hasRentalAgentId && $hasMarketingAgentId) {
            $this->newLine();
            $this->info('📈 Foreign Key Data Status:');
            
            // Count rental codes with foreign keys
            $totalRentalCodes = RentalCode::count();
            $withRentalAgentId = RentalCode::whereNotNull('rental_agent_id')->count();
            $withMarketingAgentId = RentalCode::whereNotNull('marketing_agent_id')->count();
            
            $this->table(
                ['Metric', 'Count', 'Percentage'],
                [
                    ['Total Rental Codes', $totalRentalCodes, '100%'],
                    ['With rental_agent_id', $withRentalAgentId, $totalRentalCodes > 0 ? round(($withRentalAgentId / $totalRentalCodes) * 100, 1) . '%' : '0%'],
                    ['With marketing_agent_id', $withMarketingAgentId, $totalRentalCodes > 0 ? round(($withMarketingAgentId / $totalRentalCodes) * 100, 1) . '%' : '0%'],
                ]
            );

            // Check for any null foreign keys
            $nullRentalAgent = RentalCode::whereNull('rental_agent_id')->count();
            $nullMarketingAgent = RentalCode::whereNull('marketing_agent_id')->count();
            
            if ($nullRentalAgent > 0 || $nullMarketingAgent > 0) {
                $this->newLine();
                $this->warn('⚠️  Found rental codes with null foreign keys:');
                if ($nullRentalAgent > 0) {
                    $this->line("   - {$nullRentalAgent} rental codes with null rental_agent_id");
                }
                if ($nullMarketingAgent > 0) {
                    $this->line("   - {$nullMarketingAgent} rental codes with null marketing_agent_id");
                }
            }

            // Check agent users
            $agentUsers = User::where('role', 'agent')->count();
            $this->newLine();
            $this->info("👥 Found {$agentUsers} agent users in the system");
        }

        // Migration status summary
        $this->newLine();
        if (!$hasAgentName && !$hasMarketingAgent && $hasRentalAgentId && $hasMarketingAgentId) {
            $this->info('✅ Migration appears to be COMPLETE!');
            $this->line('   - Old columns (agent_name, marketing_agent) have been removed');
            $this->line('   - New columns (rental_agent_id, marketing_agent_id) are present');
        } elseif ($hasAgentName || $hasMarketingAgent) {
            $this->warn('⚠️  Migration appears to be INCOMPLETE!');
            $this->line('   - Old columns still exist and need to be removed');
        } else {
            $this->error('❌ Migration status unclear - please check manually');
        }
    }
}
