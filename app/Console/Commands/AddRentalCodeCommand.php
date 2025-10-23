<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalCode;
use App\Models\Client;
use Carbon\Carbon;

class AddRentalCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rental-code:add-cc0120';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Client Code CC0120 to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Check if CC0120 already exists
            $existingCode = RentalCode::where('rental_code', 'CC0120')->first();
            
            if ($existingCode) {
                $this->info('Client Code CC0120 already exists in the database.');
                return;
            }

            // Create a dummy client if none exists
            $client = Client::first();
            if (!$client) {
                $client = Client::create([
                    'full_name' => 'Dummy Client for CC0120',
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
                $this->info('Created dummy client for CC0120.');
            }

            // Create the Client Code CC0120
            $rentalCode = RentalCode::create([
                'rental_code' => 'CC0120',
                'client_id' => $client->id,
                'rental_date' => Carbon::now()->subDays(1), // Yesterday
                'consultation_fee' => 100.00,
                'payment_method' => 'Cash',
                'status' => 'completed',
                'rent_by_agent' => 'System Admin',
                'client_by_agent' => 'System Admin',
                'marketing_agent' => null,
                'client_count' => 1,
                'paid' => true,
                'paid_at' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1)
            ]);

            $this->info('Successfully added Client Code CC0120 to the database.');
            $this->info('Client Code ID: ' . $rentalCode->id);
            $this->info('Client ID: ' . $client->id);
            
        } catch (\Exception $e) {
            $this->error('Failed to add Client Code CC0120: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
