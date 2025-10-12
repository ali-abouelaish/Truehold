<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check if CC0155 already exists
$existingCode = \App\Models\RentalCode::where('rental_code', 'CC0155')->first();

if ($existingCode) {
    echo "Rental code CC0155 already exists (ID: " . $existingCode->id . ")" . PHP_EOL;
    echo "Created: " . $existingCode->created_at . PHP_EOL;
    echo "Status: " . $existingCode->status . PHP_EOL;
    echo "Client: " . ($existingCode->client ? $existingCode->client->full_name : 'No client linked') . PHP_EOL;
} else {
    // Create a new rental code with CC0155
    $rentalCode = new \App\Models\RentalCode();
    $rentalCode->rental_code = 'CC0155';
    $rentalCode->rental_date = now();
    $rentalCode->consultation_fee = 300.00;
    $rentalCode->payment_method = 'Cash';
    $rentalCode->property = 'Sample Property';
    $rentalCode->licensor = 'Sample Licensor';
    $rentalCode->rent_by_agent = 'System';
    $rentalCode->marketing_agent = 'System';
    $rentalCode->client_count = 1;
    $rentalCode->status = 'pending';
    $rentalCode->paid = false;
    $rentalCode->agent_name = 'System';

    // Create a sample client for the rental code
    $client = new \App\Models\Client();
    $client->full_name = 'Sample Client';
    $client->date_of_birth = '1990-01-01';
    $client->phone_number = '+44 7700 900000';
    $client->email = 'sample@example.com';
    $client->nationality = 'British';
    $client->current_address = '123 Sample Street, London';
    $client->company_university_name = 'Sample Company';
    $client->position_role = 'Sample Role';
    $client->registration_status = 'registered';
    $client->save();

    // Link the client to the rental code
    $rentalCode->client_id = $client->id;
    $rentalCode->save();

    echo "Created new rental code: CC0155 (ID: " . $rentalCode->id . ")" . PHP_EOL;
    echo "Created sample client: " . $client->full_name . " (ID: " . $client->id . ")" . PHP_EOL;
}
