<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AgentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test agent users
        $agents = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@test.com',
                'password' => Hash::make('password123'),
                'role' => 'agent',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@test.com',
                'password' => Hash::make('password123'),
                'role' => 'agent',
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike.wilson@test.com',
                'password' => Hash::make('password123'),
                'role' => 'agent',
            ],
            [
                'name' => 'Lisa Brown',
                'email' => 'lisa.brown@test.com',
                'password' => Hash::make('password123'),
                'role' => 'agent',
            ],
        ];

        foreach ($agents as $agentData) {
            User::updateOrCreate(
                ['email' => $agentData['email']],
                $agentData
            );
        }

        $this->command->info('Agent users created/updated:');
        foreach ($agents as $agent) {
            $this->command->info("- {$agent['name']} ({$agent['email']}) - password: password123");
        }
    }
}
