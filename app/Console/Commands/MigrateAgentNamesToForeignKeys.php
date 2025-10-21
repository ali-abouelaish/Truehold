<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateAgentNamesToForeignKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:agent-names-to-foreign-keys {--dry-run : Show what would be migrated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing agent names to foreign keys in rental codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🚀 Starting agent name to foreign key migration...');

        // Get all users with agent role
        $agentUsers = User::where('role', 'agent')->get()->keyBy('name');
        $agentUsersById = User::where('role', 'agent')->get()->keyBy('id');
        $this->info("📊 Found {$agentUsers->count()} agent users");

        // Check if old columns still exist
        if (!Schema::hasColumn('rental_codes', 'agent_name') && !Schema::hasColumn('rental_codes', 'marketing_agent')) {
            $this->info('✅ Migration already completed - old columns (agent_name, marketing_agent) have been removed');
            $this->info('💡 Use "php artisan check:foreign-keys-status" to verify the current state');
            return;
        }

        // Get all rental codes that need migration
        $query = RentalCode::query();
        
        if (Schema::hasColumn('rental_codes', 'agent_name')) {
            $query->whereNotNull('agent_name');
        }
        
        if (Schema::hasColumn('rental_codes', 'marketing_agent')) {
            $query->orWhereNotNull('marketing_agent');
        }
        
        $rentalCodes = $query->get();

        $this->info("📋 Found {$rentalCodes->count()} rental codes to process");

        $migratedRentalAgent = 0;
        $migratedMarketingAgent = 0;
        $unmatchedRentalAgent = 0;
        $unmatchedMarketingAgent = 0;
        $unmatchedNames = collect();

        DB::transaction(function () use (
            $rentalCodes, 
            $agentUsers, 
            $agentUsersById,
            $isDryRun,
            &$migratedRentalAgent,
            &$migratedMarketingAgent,
            &$unmatchedRentalAgent,
            &$unmatchedMarketingAgent,
            &$unmatchedNames
        ) {
            foreach ($rentalCodes as $code) {
                $updated = false;

                // Migrate rental agent (only if column exists)
                if (Schema::hasColumn('rental_codes', 'agent_name') && !empty($code->agent_name)) {
                    $agentName = trim($code->agent_name);
                    
                    // Try exact match first
                    if ($agentUsers->has($agentName)) {
                        if (!$isDryRun) {
                            $code->rental_agent_id = $agentUsers[$agentName]->id;
                            $updated = true;
                        }
                        $migratedRentalAgent++;
                        $this->line("✅ Rental Agent: '{$agentName}' → User ID: {$agentUsers[$agentName]->id}");
                    } else {
                        // Try case-insensitive match
                        $matchedUser = $agentUsers->first(function ($user) use ($agentName) {
                            return strcasecmp($user->name, $agentName) === 0;
                        });
                        
                        if ($matchedUser) {
                            if (!$isDryRun) {
                                $code->rental_agent_id = $matchedUser->id;
                                $updated = true;
                            }
                            $migratedRentalAgent++;
                            $this->line("✅ Rental Agent: '{$agentName}' → User ID: {$matchedUser->id} (case-insensitive match)");
                        } else {
                            $unmatchedRentalAgent++;
                            $unmatchedNames->push("Rental Agent: '{$agentName}'");
                            $this->warn("❌ No match found for rental agent: '{$agentName}'");
                        }
                    }
                }

                // Migrate marketing agent (only if column exists)
                if (Schema::hasColumn('rental_codes', 'marketing_agent') && !empty($code->marketing_agent)) {
                    $marketingAgentValue = trim($code->marketing_agent);
                    
                    // Check if it's already an ID
                    if (is_numeric($marketingAgentValue) && $agentUsersById->has((int)$marketingAgentValue)) {
                        if (!$isDryRun) {
                            $code->marketing_agent_id = (int)$marketingAgentValue;
                            $updated = true;
                        }
                        $migratedMarketingAgent++;
                        $this->line("✅ Marketing Agent ID: '{$marketingAgentValue}' → User ID: {$marketingAgentValue}");
                    } else {
                        // Try exact name match
                        if ($agentUsers->has($marketingAgentValue)) {
                            if (!$isDryRun) {
                                $code->marketing_agent_id = $agentUsers[$marketingAgentValue]->id;
                                $updated = true;
                            }
                            $migratedMarketingAgent++;
                            $this->line("✅ Marketing Agent: '{$marketingAgentValue}' → User ID: {$agentUsers[$marketingAgentValue]->id}");
                        } else {
                            // Try case-insensitive match
                            $matchedUser = $agentUsers->first(function ($user) use ($marketingAgentValue) {
                                return strcasecmp($user->name, $marketingAgentValue) === 0;
                            });
                            
                            if ($matchedUser) {
                                if (!$isDryRun) {
                                    $code->marketing_agent_id = $matchedUser->id;
                                    $updated = true;
                                }
                                $migratedMarketingAgent++;
                                $this->line("✅ Marketing Agent: '{$marketingAgentValue}' → User ID: {$matchedUser->id} (case-insensitive match)");
                            } else {
                                $unmatchedMarketingAgent++;
                                $unmatchedNames->push("Marketing Agent: '{$marketingAgentValue}'");
                                $this->warn("❌ No match found for marketing agent: '{$marketingAgentValue}'");
                            }
                        }
                    }
                }

                // Save the updated rental code
                if ($updated && !$isDryRun) {
                    $code->save();
                }
            }
        });

        // Display summary
        $this->newLine();
        $this->info('📊 Migration Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Rental Agents Migrated', $migratedRentalAgent],
                ['Marketing Agents Migrated', $migratedMarketingAgent],
                ['Unmatched Rental Agents', $unmatchedRentalAgent],
                ['Unmatched Marketing Agents', $unmatchedMarketingAgent],
            ]
        );

        if ($unmatchedNames->isNotEmpty()) {
            $this->newLine();
            $this->warn('⚠️  Unmatched Agent Names:');
            $unmatchedNames->each(function ($name) {
                $this->line("   - {$name}");
            });
            $this->newLine();
            $this->warn('💡 You may need to manually create these agents or update the names to match existing users.');
        }

        if ($isDryRun) {
            $this->newLine();
            $this->info('🔍 This was a dry run. Run the command without --dry-run to apply changes.');
        } else {
            $this->newLine();
            $this->info('✅ Migration completed successfully!');
        }
    }
}