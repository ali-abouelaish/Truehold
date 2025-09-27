<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Run existing seeders
        $this->call([
            AgentSeeder::class,
            AgentUserSeeder::class,
            ClientSeeder::class,
            PropertySeeder::class,
            InvoiceSeeder::class,
        ]);

        // Add CallLogSeeder if you want sample data
        // Uncomment the line below to seed sample call logs
        // $this->call(CallLogSeeder::class);
    }
}
