<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RentalCode;
use App\Models\Client;
use Carbon\Carbon;

class RentalCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a dummy client if none exists
        $client = Client::first();
        if (!$client) {
            $client = Client::create([
                'full_name' => 'Dummy Client for Seeder',
                'email' => 'dummy@example.com',
                'phone_number' => '0000000000',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'Unknown',
                'current_address' => 'Unknown Address',
                'company_university_name' => 'Unknown',
                'company_university_address' => 'Unknown',
                'position_role' => 'Unknown',
                'current_landlord_contact_info' => 'Unknown'
            ]);
            $this->command->info('Created dummy client for seeder.');
        }

        // Create rental codes starting from CC0120
        $rentalCodes = [
            [
                'rental_code' => 'CC0120',
                'rental_date' => Carbon::now()->subDays(5),
                'consultation_fee' => 100.00,
                'payment_method' => 'Cash',
                'status' => 'completed',
                'rent_by_agent' => 'System Admin',
                'marketing_agent' => null,
                'client_count' => 1,
                'paid' => true,
                'paid_at' => Carbon::now()->subDays(5),
            ],
            [
                'rental_code' => 'CC0121',
                'rental_date' => Carbon::now()->subDays(4),
                'consultation_fee' => 150.00,
                'payment_method' => 'Transfer',
                'status' => 'completed',
                'rent_by_agent' => 'System Admin',
                'marketing_agent' => null,
                'client_count' => 1,
                'paid' => true,
                'paid_at' => Carbon::now()->subDays(4),
            ],
            [
                'rental_code' => 'CC0122',
                'rental_date' => Carbon::now()->subDays(3),
                'consultation_fee' => 200.00,
                'payment_method' => 'Cash',
                'status' => 'approved',
                'rent_by_agent' => 'System Admin',
                'marketing_agent' => null,
                'client_count' => 1,
                'paid' => false,
                'paid_at' => null,
            ]
        ];

        foreach ($rentalCodes as $codeData) {
            // Check if rental code already exists
            $existingCode = RentalCode::where('rental_code', $codeData['rental_code'])->first();
            
            if (!$existingCode) {
                $codeData['client_id'] = $client->id;
                $codeData['created_at'] = $codeData['rental_date'];
                $codeData['updated_at'] = $codeData['rental_date'];
                
                RentalCode::create($codeData);
                $this->command->info("Created rental code: {$codeData['rental_code']}");
            } else {
                $this->command->info("Rental code {$codeData['rental_code']} already exists, skipping.");
            }
        }

        $this->command->info('Rental code seeder completed!');
    }
}
