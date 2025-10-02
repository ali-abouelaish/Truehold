<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserPermission;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all available sections
        $sections = [
            'dashboard',
            'properties',
            'clients',
            'rental-codes',
            'call-logs',
            'invoices',
            'viewings',
            'admin-permissions'
        ];

        // Give full admin permissions to user ID 2
        $adminUserId = 2;
        
        // Check if user exists
        $user = User::find($adminUserId);
        if (!$user) {
            $this->command->error("User with ID {$adminUserId} not found!");
            return;
        }

        $this->command->info("Granting admin permissions to user: {$user->email}");

        foreach ($sections as $section) {
            // Delete existing permission if it exists
            UserPermission::where('user_id', $adminUserId)
                ->where('section', $section)
                ->delete();
                
            // Create new permission with access
            UserPermission::create([
                'user_id' => $adminUserId,
                'section' => $section,
                'can_access' => true,
            ]);

            $this->command->info("✅ Added access permission for '{$section}'");
        }

        $this->command->info("🎉 Successfully granted admin permissions to user ID {$adminUserId}!");
        $this->command->info("User {$user->email} can now access all admin sections and manage other users.");
    }
}
