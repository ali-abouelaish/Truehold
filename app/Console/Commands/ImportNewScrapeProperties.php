<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportNewScrapeProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:import-newscrape {--truncate : Truncate existing properties before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import properties from newscrape.csv file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvFile = 'newscrape.csv';
        
        if (!file_exists($csvFile)) {
            $this->error("CSV file '{$csvFile}' not found!");
            return 1;
        }

        $this->info("Starting import from {$csvFile}...");

        // Check if we should truncate existing properties
        if ($this->option('truncate')) {
            if ($this->confirm('This will delete ALL existing properties. Are you sure?')) {
                $this->info('Truncating existing properties...');
                Property::truncate();
                $this->info('Existing properties deleted.');
            } else {
                $this->info('Import cancelled.');
                return 0;
            }
        }

        // Read CSV file
        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            $this->error("Could not open CSV file!");
            return 1;
        }

        // Read headers
        $headers = fgetcsv($handle);
        if (!$headers) {
            $this->error("Could not read CSV headers!");
            fclose($handle);
            return 1;
        }

        $this->info("CSV Headers: " . implode(', ', $headers));

        $count = 0;
        $errors = 0;
        $batchSize = 100;
        $batch = [];

        // Skip header row and start processing
        while (($row = fgetcsv($handle)) !== false) {
            $count++;
            
            try {
                // Create data array from CSV row
                $data = array_combine($headers, $row);
                
                // Clean and validate data
                $propertyData = $this->cleanPropertyData($data);
                
                // Add to batch
                $batch[] = $propertyData;
                
                // Process batch when it reaches the batch size
                if (count($batch) >= $batchSize) {
                    $this->processBatch($batch);
                    $this->info("Processed {$count} properties...");
                    $batch = [];
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->warn("Error processing row {$count}: " . $e->getMessage());
            }
        }

        // Process remaining batch
        if (!empty($batch)) {
            $this->processBatch($batch);
        }

        fclose($handle);

        $this->info("Import completed!");
        $this->info("Total properties processed: {$count}");
        $this->info("Errors: {$errors}");

        return 0;
    }

    /**
     * Clean and validate property data from CSV.
     */
    private function cleanPropertyData($data)
    {
        // Clean and format the data
        $cleanData = [];
        
        foreach ($data as $key => $value) {
            $cleanValue = trim($value);
            
            // Handle specific field cleaning
            switch ($key) {
                case 'latitude':
                case 'longitude':
                    $cleanValue = is_numeric($cleanValue) ? (float) $cleanValue : null;
                    break;
                    
                case 'price':
                    // Remove currency symbols and convert to numeric
                    $cleanValue = preg_replace('/[^0-9.]/', '', $cleanValue);
                    $cleanValue = is_numeric($cleanValue) ? (float) $cleanValue : null;
                    break;
                    
                case 'photo_count':
                    $cleanValue = is_numeric($cleanValue) ? (int) $cleanValue : null;
                    break;
                    
                case 'min_age':
                case 'max_age':
                    $cleanValue = is_numeric($cleanValue) ? (int) $cleanValue : null;
                    break;
                    
                case 'photos':
                    // Handle JSON array of photos
                    if (!empty($cleanValue) && $cleanValue !== '[]') {
                        try {
                            $decoded = json_decode($cleanValue, true);
                            if (is_array($decoded)) {
                                $cleanValue = $decoded;
                            }
                        } catch (\Exception $e) {
                            $cleanValue = null;
                        }
                    }
                    break;
                    
                case 'all_photos':
                    // Handle comma-separated photo URLs
                    if (!empty($cleanValue)) {
                        $photos = array_map('trim', explode(',', $cleanValue));
                        $cleanValue = array_filter($photos, function($url) {
                            return filter_var($url, FILTER_VALIDATE_URL);
                        });
                    }
                    break;
                    
                default:
                    // For other fields, just clean whitespace
                    $cleanValue = empty($cleanValue) ? null : $cleanValue;
                    break;
            }
            
            $cleanData[$key] = $cleanValue;
        }

        return $cleanData;
    }

    /**
     * Process a batch of properties.
     */
    private function processBatch($batch)
    {
        try {
            DB::beginTransaction();
            
            foreach ($batch as $propertyData) {
                // Check if property already exists by link
                if (!empty($propertyData['link'])) {
                    $existing = Property::where('link', $propertyData['link'])->first();
                    if ($existing) {
                        // Update existing property
                        $existing->update($propertyData);
                    } else {
                        // Create new property
                        Property::create($propertyData);
                    }
                } else {
                    // Create new property without link
                    Property::create($propertyData);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
