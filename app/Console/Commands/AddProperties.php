<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;

class AddProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:add {file? : Path to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new properties to existing database without clearing current data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? 'new_properties_for_import_exact.csv';
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Adding properties from: {$filePath}");
        
        // Get current count
        $currentCount = Property::count();
        $this->info("Current properties in database: {$currentCount}");

        // Read CSV file
        $csv = array_map('str_getcsv', file($filePath));
        $headers = array_shift($csv);
        
        $this->info("Found headers: " . implode(', ', $headers));
        $this->info("Found " . count($csv) . " properties to add");

        $added = 0;
        $skipped = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($csv));
        $bar->start();

        foreach ($csv as $index => $row) {
            try {
                // Check if property already exists by URL
                $url = $row[0] ?? null;
                if (!$url) {
                    $skipped++;
                    continue;
                }

                $existingProperty = Property::where('url', $url)->first();
                if ($existingProperty) {
                    $skipped++;
                    continue;
                }

                // Create new property
                $propertyData = [
                    'url' => $row[0] ?? null,
                    'title' => $row[1] ?? null,
                    'location' => $row[2] ?? null,
                    'latitude' => $row[3] ?? null,
                    'longitude' => $row[4] ?? null,
                    'price' => $row[5] ?? null,
                    'description' => $row[6] ?? null,
                    'property_type' => $row[7] ?? null,
                    'available_date' => $row[8] ?? null,
                    'photo_count' => $row[9] ?? 0,
                    'first_photo_url' => $row[10] ?? null,
                    'all_photos' => $row[11] ?? null,
                    'contact_info' => $row[12] ?? null,
                    'management_company' => $row[13] ?? null,
                    'amenities' => $row[14] ?? null,
                    'age' => $row[15] ?? null,
                    'ages' => $row[16] ?? null,
                    'any_pets' => $row[17] ?? null,
                    'available' => $row[18] ?? null,
                    'balcony_roof_terrace' => $row[19] ?? null,
                    'bills_included' => $row[20] ?? null,
                    'broadband_included' => $row[21] ?? null,
                    'couples_allowed' => $row[22] ?? null,
                    'deposit' => $row[23] ?? null,
                    'deposit_room_1' => $row[24] ?? null,
                    'deposit_room_2' => $row[25] ?? null,
                    'deposit_room_3' => $row[26] ?? null,
                    'deposit_room_4' => $row[27] ?? null,
                    'disabled_access' => $row[28] ?? null,
                    'furnishings' => $row[29] ?? null,
                    'garage' => $row[30] ?? null,
                    'garden_patio' => $row[31] ?? null,
                    'gender' => $row[32] ?? null,
                    'living_room' => $row[33] ?? null,
                    'max_age' => $row[34] ?? null,
                    'maximum_term' => $row[35] ?? null,
                    'min_age' => $row[36] ?? null,
                    'minimum_term' => $row[37] ?? null,
                    'number_housemates' => $row[38] ?? null,
                    'number_flatmates' => $row[39] ?? null,
                    'occupation' => $row[40] ?? null,
                    'parking' => $row[41] ?? null,
                    'pets_allowed' => $row[42] ?? null,
                    'price_pcm' => $row[43] ?? null,
                    'references' => $row[44] ?? null,
                    'room_type' => $row[45] ?? null,
                    'smalloweding_allowed' => $row[46] ?? null,
                    'smoker' => $row[47] ?? null,
                    'total_number_rooms' => $row[48] ?? null,
                    'vegetarian_vegan' => $row[49] ?? null,
                ];

                // Clean and validate data
                foreach ($propertyData as $key => $value) {
                    if ($value === 'N/A' || $value === '') {
                        $propertyData[$key] = null;
                    }
                }

                Property::create($propertyData);
                $added++;

            } catch (\Exception $e) {
                $this->error("Failed to add property at row " . ($index + 2) . ": " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Final summary
        $finalCount = Property::count();
        $this->info("Import completed!");
        $this->info("Added: {$added} properties");
        $this->info("Skipped: {$skipped} properties (duplicates or invalid)");
        $this->info("Errors: {$errors} properties");
        $this->info("Total properties in database: {$finalCount}");

        return 0;
    }
}
