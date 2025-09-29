<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateUsersToAgentRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-to-agent-role {--role=agent : Role to assign (agent or marketing_agent)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all existing users to have agent or marketing agent role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = $this->option('role');
        
        if (!in_array($role, ['agent', 'marketing_agent'])) {
            $this->error('Invalid role. Please use "agent" or "marketing_agent"');
            return 1;
        }

        $this->info("Updating all users to {$role} role...");

        // Get all users
        $users = User::all();
        $totalUsers = $users->count();

        if ($totalUsers === 0) {
            $this->info('No users found to update.');
            return 0;
        }

        $this->info("Found {$totalUsers} users to update.");

        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            // Check if user already has a role
            if ($user->role && $user->role !== 'user') {
                $this->line("Skipping {$user->name} - already has role: {$user->role}");
                $skippedCount++;
                continue;
            }

            // Update user role
            $user->update(['role' => $role]);
            $this->line("Updated {$user->name} to {$role} role");
            $updatedCount++;
        }

        $this->info("\nUpdate completed!");
        $this->info("Users updated: {$updatedCount}");
        $this->info("Users skipped: {$skippedCount}");
        $this->info("Total users: {$totalUsers}");

        return 0;
    }
}
