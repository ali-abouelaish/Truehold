<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class ImportProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:import {file? : Path to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import properties from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file') ?? 'results_detailed.csv';
        
        if (!file_exists($filePath)) {
            $this->error("CSV file not found: {$filePath}");
            return 1;
        }

        $this->info("Starting import from: {$filePath}");
        
        // Clear existing properties
        Property::truncate();
        $this->info("Cleared existing properties");

        // Read the CSV file content
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        
        // Find the first line that looks like headers (contains 'url' and other expected fields)
        $headerLine = null;
        $dataStartIndex = 0;
        
        for ($i = 0; $i < count($lines); $i++) {
            if (strpos($lines[$i], 'url,title,location') !== false) {
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

        foreach ($csv as $row) {
            // Create a basic property data structure with essential fields
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
            ];

            // Clean up the data
            foreach ($propertyData as $key => $value) {
                if ($value === 'N/A' || $value === '') {
                    $propertyData[$key] = null;
                }
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
        
        $this->info("Import completed!");
        $this->info("Imported: {$imported} properties");
        $this->info("Skipped: {$skipped} properties");
        
        return 0;
    }
}
