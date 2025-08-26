<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Property;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Starting property import...\n";

// Read the CSV file
$csvFile = 'legends_scraped_results.csv';
if (!file_exists($csvFile)) {
    echo "Error: CSV file not found: $csvFile\n";
    exit(1);
}

$handle = fopen($csvFile, 'r');
if (!$handle) {
    echo "Error: Cannot open CSV file\n";
    exit(1);
}

// Read header
$headers = fgetcsv($handle);
if (!$headers) {
    echo "Error: Cannot read CSV headers\n";
    exit(1);
}

echo "CSV Headers: " . implode(', ', $headers) . "\n";

$imported = 0;
$skipped = 0;
$errors = 0;

// Read data rows
while (($row = fgetcsv($handle)) !== false) {
    if (count($row) !== count($headers)) {
        echo "Warning: Skipping row with mismatched columns: " . implode(', ', $row) . "\n";
        $skipped++;
        continue;
    }
    
    // Create associative array
    $data = array_combine($headers, $row);
    
    // Skip if URL is empty
    if (empty($data['url'])) {
        $skipped++;
        continue;
    }
    
    try {
        // Check if property already exists
        $existing = Property::where('url', $data['url'])->first();
        if ($existing) {
            echo "Property already exists: {$data['title']}\n";
            $skipped++;
            continue;
        }
        
        // Clean and prepare data
        $propertyData = [
            'url' => $data['url'],
            'title' => $data['title'] ?? null,
            'location' => $data['location'] ?? null,
            'latitude' => !empty($data['latitude']) ? (float)$data['latitude'] : null,
            'longitude' => !empty($data['longitude']) ? (float)$data['longitude'] : null,
            'status' => $data['status'] ?? 'available',
            'price' => $data['price'] ?? null,
            'description' => $data['description'] ?? null,
            'property_type' => $data['property_type'] ?? null,
            'available_date' => $data['available_date'] ?? null,
            'photo_count' => !empty($data['photo_count']) ? (int)$data['photo_count'] : 0,
            'first_photo_url' => $data['first_photo_url'] ?? null,
            'all_photos' => $data['all_photos'] ?? null,
            'photos' => $data['photos'] ?? null,
            'contact_info' => $data['contact_info'] ?? null,
            'management_company' => $data['management_company'] ?? null,
            'amenities' => $data['amenities'] ?? null,
            'age' => $data['age'] ?? null,
            'ages' => $data['ages'] ?? null,
            'any_pets' => $data['any_pets'] ?? null,
            'available' => $data['available'] ?? null,
            'balcony_roof_terrace' => $data['balcony_roof_terrace'] ?? null,
            'bills_included' => $data['bills_included'] ?? null,
            'broadband_included' => $data['broadband_included'] ?? null,
            'couples_allowed' => $data['couples_allowed'] ?? null,
            'deposit' => $data['deposit'] ?? null,
            'deposit_room_1' => $data['deposit_room_1'] ?? null,
            'deposit_room_2' => $data['deposit_room_2'] ?? null,
            'deposit_room_3' => $data['deposit_room_3'] ?? null,
            'deposit_room_4' => $data['deposit_room_4'] ?? null,
            'disabled_access' => $data['disabled_access'] ?? null,
            'furnishings' => $data['furnishings'] ?? null,
            'garage' => $data['garage'] ?? null,
            'garden_patio' => $data['garden_patio'] ?? null,
            'gender' => $data['gender'] ?? null,
            'living_room' => $data['living_room'] ?? null,
            'max_age' => $data['max_age'] ?? null,
            'maximum_term' => $data['maximum_term'] ?? null,
            'min_age' => $data['min_age'] ?? null,
            'minimum_term' => $data['minimum_term'] ?? null,
            'number' => $data['number'] ?? null,
        ];
        
        // Create property
        $property = Property::create($propertyData);
        echo "Imported: {$property->title}\n";
        $imported++;
        
    } catch (Exception $e) {
        echo "Error importing property: " . $e->getMessage() . "\n";
        $errors++;
    }
}

fclose($handle);

echo "\nImport completed!\n";
echo "Imported: $imported\n";
echo "Skipped: $skipped\n";
echo "Errors: $errors\n";

