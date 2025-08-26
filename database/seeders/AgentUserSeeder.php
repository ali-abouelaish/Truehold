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
        // Create a test agent user
        User::create([
            'name' => 'Test Agent',
            'email' => 'agent@test.com',
            'password' => Hash::make('password123'),
        ]);

        $this->command->info('Test agent user created: agent@test.com / password123');
    }
}
