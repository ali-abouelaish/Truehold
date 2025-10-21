<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateMissingAgents extends Command
{
    protected $signature = 'create:missing-agents {--dry-run : Show what would be created without making changes}';
    protected $description = 'Create missing agents for unmatched rental codes';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $this->info('🚀 Creating missing agents for unmatched rental codes...');

        // Get current agent users
        $existingAgents = User::where('role', 'agent')->get()->keyBy('name');
        $this->info("📊 Found {$existingAgents->count()} existing agent users");

        // Create missing agents
        $agentsToCreate = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@truehold.co.uk',
                'role' => 'agent',
                'password' => bcrypt('password123'), // Default password
            ],
            [
                'name' => 'Marketing Agent 1',
                'email' => 'marketing1@truehold.co.uk',
                'role' => 'agent',
                'password' => bcrypt('password123'), // Default password
            ],
        ];

        $createdAgents = 0;
        $skippedAgents = 0;

        DB::transaction(function () use ($agentsToCreate, $existingAgents, $isDryRun, &$createdAgents, &$skippedAgents) {
            foreach ($agentsToCreate as $agentData) {
                // Check if agent already exists
                if ($existingAgents->has($agentData['name'])) {
                    $skippedAgents++;
                    $this->line("⏭️  Agent '{$agentData['name']}' already exists");
                    continue;
                }

                // Check if email already exists
                $existingUser = User::where('email', $agentData['email'])->first();
                if ($existingUser) {
                    $skippedAgents++;
                    $this->warn("⚠️  Email '{$agentData['email']}' already exists for user: {$existingUser->name}");
                    continue;
                }

                if (!$isDryRun) {
                    $agent = User::create($agentData);
                    $createdAgents++;
                    $this->line("✅ Created agent: '{$agent->name}' (ID: {$agent->id})");
                } else {
                    $createdAgents++;
                    $this->line("✅ Would create agent: '{$agentData['name']}' with email: {$agentData['email']}");
                }
            }
        });

        // Display summary
        $this->newLine();
        $this->info('📊 Agent Creation Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Agents Created', $createdAgents],
                ['Agents Skipped', $skippedAgents],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->info('🔍 This was a dry run. Run the command without --dry-run to create agents.');
        } else {
            $this->newLine();
            $this->info('✅ Agent creation completed!');
            $this->info('💡 Default password for new agents: password123');
            $this->info('💡 Agents should change their passwords on first login.');
        }
    }
}