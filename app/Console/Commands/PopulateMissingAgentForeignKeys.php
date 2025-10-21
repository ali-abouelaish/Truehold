<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PopulateMissingAgentForeignKeys extends Command
{
    protected $signature = 'populate:missing-agent-foreign-keys {--dry-run : Show what would be updated without making changes}';
    protected $description = 'Populate missing agent foreign keys in rental codes';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🚀 Starting to populate missing agent foreign keys...');

        // Get all agent users
        $agentUsers = User::where('role', 'agent')->get()->keyBy('name');
        $this->info("📊 Found {$agentUsers->count()} agent users");

        // Get rental codes with missing foreign keys
        $rentalCodes = RentalCode::where(function($query) {
            $query->whereNull('rental_agent_id')
                  ->orWhereNull('marketing_agent_id');
        })->where(function($query) {
            $query->whereNotNull('agent_name')
                  ->orWhereNotNull('marketing_agent');
        })->get();

        $this->info("📋 Found {$rentalCodes->count()} rental codes with missing foreign keys");

        $updatedRentalAgent = 0;
        $updatedMarketingAgent = 0;
        $unmatchedRentalAgent = 0;
        $unmatchedMarketingAgent = 0;
        $unmatchedNames = collect();

        DB::transaction(function () use (
            $rentalCodes, 
            $agentUsers, 
            $isDryRun,
            &$updatedRentalAgent,
            &$updatedMarketingAgent,
            &$unmatchedRentalAgent,
            &$unmatchedMarketingAgent,
            &$unmatchedNames
        ) {
            foreach ($rentalCodes as $code) {
                $updated = false;

                // Handle rental agent
                if (empty($code->rental_agent_id) && !empty($code->agent_name)) {
                    $agentName = trim($code->agent_name);
                    
                    // Try exact match
                    if ($agentUsers->has($agentName)) {
                        if (!$isDryRun) {
                            $code->rental_agent_id = $agentUsers[$agentName]->id;
                            $updated = true;
                        }
                        $updatedRentalAgent++;
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
                            $updatedRentalAgent++;
                            $this->line("✅ Rental Agent: '{$agentName}' → User ID: {$matchedUser->id} (case-insensitive)");
                        } else {
                            $unmatchedRentalAgent++;
                            $unmatchedNames->push("Rental Agent: '{$agentName}'");
                            $this->warn("❌ No match found for rental agent: '{$agentName}'");
                        }
                    }
                }

                // Handle marketing agent
                if (empty($code->marketing_agent_id) && !empty($code->marketing_agent)) {
                    $marketingAgentValue = trim($code->marketing_agent);
                    
                    // Check if it's already an ID
                    if (is_numeric($marketingAgentValue)) {
                        $userId = (int)$marketingAgentValue;
                        $user = User::find($userId);
                        if ($user && $user->role === 'agent') {
                            if (!$isDryRun) {
                                $code->marketing_agent_id = $userId;
                                $updated = true;
                            }
                            $updatedMarketingAgent++;
                            $this->line("✅ Marketing Agent ID: '{$marketingAgentValue}' → User ID: {$userId}");
                        } else {
                            $unmatchedMarketingAgent++;
                            $unmatchedNames->push("Marketing Agent ID: '{$marketingAgentValue}' (user not found or not agent)");
                            $this->warn("❌ User ID {$marketingAgentValue} not found or not an agent");
                        }
                    } else {
                        // Try exact name match
                        if ($agentUsers->has($marketingAgentValue)) {
                            if (!$isDryRun) {
                                $code->marketing_agent_id = $agentUsers[$marketingAgentValue]->id;
                                $updated = true;
                            }
                            $updatedMarketingAgent++;
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
                                $updatedMarketingAgent++;
                                $this->line("✅ Marketing Agent: '{$marketingAgentValue}' → User ID: {$matchedUser->id} (case-insensitive)");
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
        $this->info('📊 Update Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Rental Agents Updated', $updatedRentalAgent],
                ['Marketing Agents Updated', $updatedMarketingAgent],
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
            $this->info('✅ Foreign key population completed successfully!');
        }
    }
}