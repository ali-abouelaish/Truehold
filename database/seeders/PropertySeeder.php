<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Starting property data import from cleaned CSV...');
        
        // Clear existing data
        Property::truncate();
        $this->command->info('🗑️  Cleared existing property data');
        
        $csvFile = base_path('results_cleaned.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error('❌ Cleaned CSV file not found. Please run the CSV cleaner first.');
            return;
        }
        
        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            $this->command->error('❌ Could not open CSV file');
            return;
        }
        
        // Skip header row
        $headers = fgetcsv($handle);
        if (!$headers) {
            $this->command->error('❌ Could not read CSV headers');
            fclose($handle);
            return;
        }
        
        $this->command->info('📋 CSV Headers: ' . implode(', ', array_slice($headers, 0, 10)) . '...');
        
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        
        // Process each row
        while (($row = fgetcsv($handle)) !== false) {
            try {
                // Ensure we have enough columns
                while (count($row) < count($headers)) {
                    $row[] = '';
                }
                
                // Create data array
                $data = array_combine($headers, $row);
                
                // Validate required fields
                if (empty($data['title']) || empty($data['location'])) {
                    $skipped++;
                    continue;
                }
                
                // Validate coordinates
                if (empty($data['latitude']) || empty($data['longitude'])) {
                    $skipped++;
                    continue;
                }
                
                // Clean and validate coordinates
                $lat = $this->cleanCoordinate($data['latitude']);
                $lng = $this->cleanCoordinate($data['longitude']);
                
                if ($lat === null || $lng === null) {
                    $skipped++;
                    continue;
                }
                
                // Create property with only existing columns
                Property::create([
                    'url' => $data['url'] ?? '',
                    'title' => $data['title'],
                    'location' => $data['location'],
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'price' => $data['price'] ?? '',
                    'description' => $data['description'] ?? '',
                    'property_type' => $data['property_type'] ?? '',
                    'available_date' => $data['available_date'] ?? '',
                    'photo_count' => is_numeric($data['photo_count']) ? (int)$data['photo_count'] : 0,
                    'first_photo_url' => $data['first_photo_url'] ?? '',
                    'all_photos' => $data['all_photos'] ?? '',
                    'contact_info' => $data['contact_info'] ?? '',
                    'management_company' => $data['management_company'] ?? '',
                    'amenities' => $data['amenities'] ?? '',
                    'age' => $data['age'] ?? '',
                    'ages' => $data['ages'] ?? '',
                    'any_pets' => $data['any_pets'] ?? '',
                    'available' => $data['available'] ?? '',
                    'balcony_roof_terrace' => $data['balcony_roof_terrace'] ?? '',
                    'bills_included' => $data['bills_included'] ?? '',
                    'broadband_included' => $data['broadband_included'] ?? '',
                    'couples_allowed' => $data['couples_allowed'] ?? '',
                    'deposit' => $data['deposit'] ?? '',
                    'deposit_room_1' => $data['deposit_room_1'] ?? '',
                    'deposit_room_2' => $data['deposit_room_2'] ?? '',
                    'deposit_room_3' => $data['deposit_room_3'] ?? '',
                    'deposit_room_4' => $data['deposit_room_4'] ?? '',
                    'disabled_access' => $data['disabled_access'] ?? '',
                    'furnishings' => $data['furnishings'] ?? '',
                    'garage' => $data['garage'] ?? '',
                    'garden_patio' => $data['garden_patio'] ?? '',
                    'gender' => $data['gender'] ?? '',
                    'living_room' => $data['living_room'] ?? '',
                    'max_age' => $data['max_age'] ?? '',
                    'maximum_term' => $data['maximum_term'] ?? '',
                    'min_age' => $data['min_age'] ?? '',
                    'minimum_term' => $data['minimum_term'] ?? '',
                    'number' => $data['number_housemates'] ?? '', // Map to existing 'number' column
                ]);
                
                $imported++;
                
                if ($imported % 10 == 0) {
                    $this->command->info("📥 Imported {$imported} properties...");
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->command->warn("⚠️  Error importing row: " . $e->getMessage());
            }
        }
        
        fclose($handle);
        
        $this->command->info('');
        $this->command->info('✅ Property import completed!');
        $this->command->info("📊 Statistics:");
        $this->command->info("   ✅ Successfully imported: {$imported}");
        $this->command->info("   ⏭️  Skipped (invalid data): {$skipped}");
        $this->command->info("   ❌ Errors: {$errors}");
        $this->command->info("   📍 Total properties with coordinates: " . Property::withValidCoordinates()->count());
        
        // Show sample of imported data
        $sample = Property::withValidCoordinates()->take(3)->get();
        if ($sample->count() > 0) {
            $this->command->info('');
            $this->command->info('📋 Sample imported properties:');
            foreach ($sample as $property) {
                $this->command->info("   • {$property->title} in {$property->location} ({$property->latitude}, {$property->longitude})");
            }
        }
    }
    
    /**
     * Clean and validate coordinate value
     */
    private function cleanCoordinate($value): ?float
    {
        if (empty($value)) {
            return null;
        }
        
        // Remove any non-numeric characters except decimal points and minus signs
        $clean = preg_replace('/[^\d.-]/', '', $value);
        
        if (empty($clean)) {
            return null;
        }
        
        try {
            $coord = (float) $clean;
            
            // Validate range
            if ($coord < -180 || $coord > 180) {
                return null;
            }
            
            return $coord;
        } catch (\Exception $e) {
            return null;
        }
    }
}
