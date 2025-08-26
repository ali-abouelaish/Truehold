<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use Illuminate\Support\Facades\Schema;

class ImportPropertiesAdvanced extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:import-advanced {file? : Path to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import properties with advanced data including detailed features and amenities';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? 'new_properties_cleaned.csv';
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Starting advanced import from: {$filePath}");

        // Clear existing properties
        Property::truncate();
        $this->info("Cleared existing properties");

        // Read the CSV file content
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        // Find the first line that looks like headers
        $headerLine = null;
        $dataStartIndex = 0;
        
        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines[$i], 'url,') !== false) {
                $headerLine = $lines[$i];
                $dataStartIndex = $i + 1;
                break;
            }
        }
        
        if (!$headerLine) {
            $this->error("Could not find valid headers in CSV file");
            return 1;
        }
        
        $headers = str_getcsv($headerLine);
        $this->info("Found headers: " . implode(', ', $headers));
        
        // Process data rows
        $csv = [];
        for ($i = $dataStartIndex; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (!empty($line) && strpos($line, 'http') !== false) {
                $csv[] = str_getcsv($line);
            }
        }
        
        $this->info("Found " . count($csv) . " properties to import");
        
        $bar = $this->output->createProgressBar(count($csv));
        $bar->start();

        $imported = 0;
        $skipped = 0;
        $additionalFields = [];

        foreach ($csv as $row) {
            // Create property data structure with all available fields
            $propertyData = [];
            
            // Map CSV columns to property fields
            foreach ($headers as $index => $header) {
                $value = $row[$index] ?? null;
                
                // Clean up the data
                if ($value === 'N/A' || $value === '') {
                    $value = null;
                }
                
                // Map common field names
                $fieldName = $this->mapFieldName($header);
                if ($fieldName) {
                    $propertyData[$fieldName] = $value;
                } else {
                    // Store unknown fields in additional_fields JSON column
                    if ($value && $value !== 'N/A') {
                        $additionalFields[$header] = $value;
                    }
                }
            }
            
            // Store additional fields as JSON
            if (!empty($additionalFields)) {
                $propertyData['additional_fields'] = json_encode($additionalFields);
                $additionalFields = []; // Reset for next property
            }

            // Validate essential fields
            if (empty($propertyData['url']) || empty($propertyData['title'])) {
                $skipped++;
                $bar->advance();
                continue;
            }

            try {
                Property::create($propertyData);
                $imported++;
            } catch (\Exception $e) {
                $this->warn("Failed to import property: " . ($propertyData['url'] ?? 'Unknown') . " - " . $e->getMessage());
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("Advanced import completed!");
        $this->info("Imported: {$imported} properties");
        $this->info("Skipped: {$skipped} properties");
        
        return 0;
    }
    
    /**
     * Map CSV field names to database column names
     */
    private function mapFieldName($csvField)
    {
        $fieldMappings = [
            'url' => 'url',
            'title' => 'title',
            'location' => 'location',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'price' => 'price',
            'description' => 'description',
            'property_type' => 'property_type',
            'available_date' => 'available_date',
            'photo_count' => 'photo_count',
            'first_photo_url' => 'first_photo_url',
            'all_photos' => 'all_photos',
            'contact_info' => 'contact_info',
            'management_company' => 'management_company',
            'amenities' => 'amenities',
            
            // Additional fields from advanced scraper
            'price_pcm' => 'price_pcm',
            'room_type' => 'room_type',
            'available' => 'available',
            'minimum_term' => 'minimum_term',
            'maximum_term' => 'maximum_term',
            'deposit' => 'deposit',
            'bills_included' => 'bills_included',
            'furnishings' => 'furnishings',
            'parking' => 'parking',
            'garage' => 'garage',
            'garden_patio' => 'garden_patio',
            'balcony_roof_terrace' => 'balcony_roof_terrace',
            'disabled_access' => 'disabled_access',
            'living_room' => 'living_room',
            'broadband_included' => 'broadband_included',
            'number_housemates' => 'number_housemates',
            'number_flatmates' => 'number_flatmates',
            'total_number_rooms' => 'total_number_rooms',
            'smoker' => 'smoker',
            'any_pets' => 'any_pets',
            'pets_allowed' => 'pets_allowed',
            'occupation' => 'occupation',
            'gender' => 'gender',
            'ages' => 'ages',
            'min_age' => 'min_age',
            'max_age' => 'max_age',
            'couples_allowed' => 'couples_allowed',
            'smalloweding_allowed' => 'smoking_allowed',
            'references' => 'references',
            'security_deposit' => 'security_deposit',
        ];
        
        return $fieldMappings[$csvField] ?? null;
    }
}
