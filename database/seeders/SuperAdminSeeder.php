<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update super admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@letconnect.com'],
            [
                'name' => 'Super Administrator',
                'email' => 'admin@letconnect.com',
                'password' => Hash::make('SuperAdmin2024!'), // Secure default password
                'role' => 'admin',
                'roles' => ['admin', 'super_admin'],
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Super Admin User: {$superAdmin->email}");
        $this->command->info("Password: SuperAdmin2024!");

        // Define all available sections
        $sections = [
            'dashboard',
            'properties', 
            'clients',
            'rental_codes',
            'call_logs',
            'invoices',
            'group_viewings',
            'users',
            'admin_permissions'
        ];

        // Grant full permissions to all sections
        foreach ($sections as $section) {
            // Delete existing permission if it exists
            UserPermission::where('user_id', $superAdmin->id)
                ->where('section', $section)
                ->delete();
                
            // Create new permission with full access
            UserPermission::create([
                'user_id' => $superAdmin->id,
                'section' => $section,
                'can_access' => true,
            ]);

            $this->command->info("✅ Granted access to '{$section}'");
        }

        $this->command->info("🎉 Super Admin created successfully!");
        $this->command->info("🔐 Login credentials:");
        $this->command->info("   Email: admin@letconnect.com");
        $this->command->info("   Password: SuperAdmin2024!");
    }
}
