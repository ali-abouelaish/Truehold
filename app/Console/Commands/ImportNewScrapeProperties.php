<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            $this->error("CSV file not found: $csvFile");
            return 1;
        }

        if ($this->option('truncate')) {
            if ($this->confirm('This will delete ALL existing properties. Are you sure?')) {
                $this->info('Truncating properties table...');
                Property::truncate();
                $this->info('Properties table truncated successfully.');
            } else {
                $this->info('Import cancelled.');
                return 0;
            }
        }

        $this->info('Starting import from newscrape.csv...');
        
        // Use a more reliable CSV parsing approach
        $handle = fopen($csvFile, 'r');
        if ($handle === false) {
            $this->error('Could not open CSV file');
            return 1;
        }

        // Read headers
        $headers = fgetcsv($handle);
        if ($headers === false) {
            $this->error('Could not read CSV headers');
            fclose($handle);
            return 1;
        }

        $this->info('CSV Headers: ' . implode(', ', $headers));
        $this->info('Total columns: ' . count($headers));

        $rowCount = 0;
        $importedCount = 0;
        $updatedCount = 0;
        $errorCount = 0;
        $batchSize = 100;
        $batch = [];

        // Process each row
        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;
            
            // Skip rows with incorrect column count
            if (count($row) !== count($headers)) {
                $this->warn("Row $rowCount: Column count mismatch. Expected " . count($headers) . ", got " . count($row));
                $errorCount++;
                continue;
            }

            // Create data array from headers and row
            $data = array_combine($headers, $row);
            
            // Clean and validate data
            $cleanedData = $this->cleanData($data);
            
            // Skip if essential data is missing
            if (empty($cleanedData['link']) || empty($cleanedData['title'])) {
                $this->warn("Row $rowCount: Missing essential data (link or title)");
                $errorCount++;
                continue;
            }
            
            // Check if property already exists (by link)
            $existingProperty = null;
            if (!empty($cleanedData['link'])) {
                $existingProperty = Property::where('link', $cleanedData['link'])->first();
            }

            if ($existingProperty) {
                // Update existing property
                try {
                    $existingProperty->update($cleanedData);
                    $updatedCount++;
                    $this->line("Updated: {$cleanedData['title']}");
                } catch (\Exception $e) {
                    $this->error("Error updating property: " . $e->getMessage());
                    $errorCount++;
                }
            } else {
                // Add to batch for creation
                $batch[] = $cleanedData;
                
                if (count($batch) >= $batchSize) {
                    $this->processBatch($batch, $importedCount);
                    $batch = [];
                }
            }

            if ($rowCount % 100 === 0) {
                $this->info("Processed $rowCount rows...");
            }
        }

        // Process remaining batch
        if (!empty($batch)) {
            $this->processBatch($batch, $importedCount);
        }

        fclose($handle);

        $this->info("\nImport completed!");
        $this->info("Total rows processed: $rowCount");
        $this->info("New properties imported: $importedCount");
        $this->info("Existing properties updated: $updatedCount");
        $this->info("Errors: $errorCount");

        return 0;
    }
    
    /**
     * Clean and validate data from CSV
     */
    private function cleanData(array $data): array
    {
        $cleaned = [];
        
        foreach ($data as $key => $value) {
            $value = trim($value);
            
            // Skip empty values
            if (empty($value) || $value === 'N/A' || $value === 'null' || $value === 'undefined') {
                continue;
            }

            // Handle specific field types
            switch ($key) {
                case 'latitude':
                case 'longitude':
                    if (is_numeric($value)) {
                        $cleaned[$key] = (float) $value;
                    }
                    break;
                    
                case 'price':
                    // Remove currency symbols and convert to numeric
                    $price = preg_replace('/[^0-9.]/', '', $value);
                    if (is_numeric($price)) {
                        $cleaned[$key] = (float) $price;
                    } else {
                        $cleaned[$key] = $value; // Keep original if can't parse
                    }
                    break;
                    
                case 'photos':
                    // Handle photos array - parse JSON or comma-separated URLs
                    $cleaned[$key] = $this->parsePhotos($value);
                    break;
                    
                case 'all_photos':
                    // Handle all_photos - parse comma-separated URLs
                    $cleaned[$key] = $this->parseAllPhotos($value);
                    break;
                    
                case 'description':
                    // Preserve description formatting but clean problematic characters
                    $cleaned[$key] = $this->cleanDescription($value);
                    break;
                    
                case 'min_age':
                case 'max_age':
                case 'photo_count':
                case 'total_rooms':
                    if (is_numeric($value)) {
                        $cleaned[$key] = (int) $value;
                    } else {
                        $cleaned[$key] = $value;
                    }
                    break;
                    
                default:
                    // Clean special characters and encoding issues
                    $cleaned[$key] = $this->cleanString($value);
                    break;
            }
        }

        return $cleaned;
    }
    
    /**
     * Clean string data to remove problematic characters
     */
    private function cleanString(string $value): string
    {
        // Convert to UTF-8 and remove BOM if present
        $value = str_replace("\xEF\xBB\xBF", '', $value);
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        
        // Remove or replace problematic Unicode characters
        $value = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $value); // Emojis
        $value = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $value); // Misc symbols
        $value = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $value); // Transport symbols
        $value = preg_replace('/[\x{1F1E0}-\x{1F1FF}]/u', '', $value); // Flag symbols
        $value = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $value);   // Misc symbols
        $value = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $value);   // Dingbats
        
        // Remove other problematic characters that cause database errors
        $value = str_replace([
            "\x80", "\x93", "\x94", "\x96", "\x97", "\x99", "\x9C", "\x9D", "\xAD",
            "\xE2\x80\x93", "\xE2\x80\x94", "\xE2\x80\x96", "\xE2\x80\x97", "\xE2\x80\x99", "\xE2\x80\x9C", "\xE2\x80\x9D"
        ], '', $value);
        
        // Remove any remaining control characters
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value);
        
        // Clean up multiple spaces and newlines
        $value = preg_replace('/\s+/', ' ', $value);
        $value = str_replace(["\r", "\n", "\t"], ' ', $value);
        
        // Ensure the string is not too long for database fields
        if (strlen($value) > 65535) {
            $value = substr($value, 0, 65535);
        }
        
        return trim($value);
    }

    /**
     * Parse photos field from CSV
     */
    private function parsePhotos($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // If it's already a JSON string, try to decode it
        if (strpos($value, '[') === 0) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        
        // If it's comma-separated URLs, split them
        if (strpos($value, ',') !== false) {
            $urls = array_map('trim', explode(',', $value));
            $urls = array_filter($urls, function($url) {
                return filter_var($url, FILTER_VALIDATE_URL);
            });
            return $urls;
        }
        
        // Single URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return [$value];
        }
        
        return null;
    }
    
    /**
     * Parse all_photos field from CSV
     */
    private function parseAllPhotos($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // If it's comma-separated URLs, split them
        if (strpos($value, ',') !== false) {
            $urls = array_map('trim', explode(',', $value));
            $urls = array_filter($urls, function($url) {
                return filter_var($url, FILTER_VALIDATE_URL);
            });
            return implode(', ', $urls);
        }
        
        // Single URL
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return $value;
    }
    
    /**
     * Clean description while preserving formatting
     */
    private function cleanDescription($value)
    {
        if (empty($value)) {
            return null;
        }
        
        // Remove BOM and convert to UTF-8
        $value = str_replace("\xEF\xBB\xBF", '', $value);
        $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        
        // Remove problematic Unicode characters but keep basic formatting
        $value = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $value); // Emojis
        $value = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $value); // Misc symbols
        $value = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $value); // Transport symbols
        $value = preg_replace('/[\x{1F1E0}-\x{1F1FF}]/u', '', $value); // Flag symbols
        $value = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $value);   // Misc symbols
        $value = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $value);   // Dingbats
        
        // Remove problematic characters that cause database errors
        $value = str_replace([
            "\x80", "\x93", "\x94", "\x96", "\x97", "\x99", "\x9C", "\x9D", "\xAD",
            "\xE2\x80\x93", "\xE2\x80\x94", "\xE2\x80\x96", "\xE2\x80\x97", "\xE2\x80\x99", "\xE2\x80\x9C", "\xE2\x80\x9D"
        ], '', $value);
        
        // Remove control characters but preserve newlines and tabs
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        
        // Clean up multiple spaces but preserve line breaks
        $value = preg_replace('/[ \t]+/', ' ', $value);
        
        // Ensure the string is not too long for database fields
        if (strlen($value) > 65535) {
            $value = substr($value, 0, 65535);
        }
        
        return trim($value);
    }

    /**
     * Process a batch of properties
     */
    private function processBatch(array $batch, int &$importedCount): void
    {
        try {
            Property::insert($batch);
            $importedCount += count($batch);
            $this->line("Imported batch of " . count($batch) . " properties");
        } catch (\Exception $e) {
            $this->error("Error importing batch: " . $e->getMessage());
            
            // Try importing one by one to identify problematic records
            foreach ($batch as $propertyData) {
                try {
                    Property::create($propertyData);
                    $importedCount++;
                } catch (\Exception $e) {
                    $this->error("Error importing property: " . ($propertyData['title'] ?? 'Unknown') . " - " . $e->getMessage());
                }
            }
        }
    }
}
