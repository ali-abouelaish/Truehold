<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Property;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class ScrapeAllVacProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:all-vac {--dry-run : Run without saving to database} {--limit= : Limit number of URLs to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape properties from all_vac.csv and add them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting to scrape properties from all_vac.csv...');
        
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit');
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No data will be saved to database');
        }
        
        // Check if all_vac.csv exists
        $csvPath = base_path('all_vac.csv');
        if (!file_exists($csvPath)) {
            $this->error('❌ all_vac.csv not found in project root');
            return 1;
        }
        
        // Read URLs from CSV
        $urls = $this->readUrlsFromCsv($csvPath);
        if (empty($urls)) {
            $this->error('❌ No URLs found in all_vac.csv');
            return 1;
        }
        
        // Apply limit if specified
        if ($limit) {
            $urls = array_slice($urls, 0, (int)$limit);
            $this->info("📊 Limited to {$limit} URLs");
        }
        
        $this->info("📋 Found " . count($urls) . " URLs to process");
        
        // Initialize counters
        $totalProcessed = 0;
        $successful = 0;
        $failed = 0;
        $skipped = 0;
        
        // Process each URL
        $progressBar = $this->output->createProgressBar(count($urls));
        $progressBar->start();
        
        foreach ($urls as $url) {
            try {
                $progressBar->advance();
                
                // Check if property already exists
                if (!$dryRun && Property::where('url', $url)->exists()) {
                    $skipped++;
                    continue;
                }
                
                // Scrape the property
                $propertyData = $this->scrapeProperty($url);
                
                if ($propertyData) {
                    if (!$dryRun) {
                        // Save to database
                        $property = new Property($propertyData);
                        $property->save();
                        $successful++;
                    } else {
                        $successful++;
                    }
                } else {
                    $failed++;
                }
                
                $totalProcessed++;
                
                // Add delay to be respectful to the server
                usleep(1000000); // 1 second delay
                
            } catch (Exception $e) {
                $failed++;
                Log::error("Failed to process URL: {$url}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Display results
        $this->info('📊 Scraping Results:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', $totalProcessed],
                ['Successful', $successful],
                ['Failed', $failed],
                ['Skipped (Already Exists)', $skipped],
            ]
        );
        
        if ($dryRun) {
            $this->warn('⚠️  This was a dry run. No data was saved to the database.');
        } else {
            $this->info('✅ Properties have been added to the database!');
        }
        
        return 0;
    }
    
    /**
     * Read URLs from CSV file
     */
    private function readUrlsFromCsv(string $csvPath): array
    {
        $urls = [];
        
        if (($handle = fopen($csvPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (!empty($data[0]) && filter_var(trim($data[0]), FILTER_VALIDATE_URL)) {
                    $urls[] = trim($data[0]);
                }
            }
            fclose($handle);
        }
        
        return $urls;
    }
    
    /**
     * Scrape a single property
     */
    private function scrapeProperty(string $url): ?array
    {
        try {
            // Add headers to mimic a real browser
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'Accept-Encoding: gzip, deflate',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
            ];
            
            $context = stream_context_create([
                'http' => [
                    'header' => implode("\r\n", $headers),
                    'timeout' => 30,
                ]
            ]);
            
            $html = file_get_contents($url, false, $context);
            if ($html === false) {
                return null;
            }
            
            // Parse HTML
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);
            
            // Extract property data
            $propertyData = $this->extractPropertyData($dom, $xpath, $url);
            
            return $propertyData;
            
        } catch (Exception $e) {
            Log::error("Error scraping property: {$url}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Extract property data from HTML
     */
    private function extractPropertyData(\DOMDocument $dom, \DOMXPath $xpath, string $url): array
    {
        // Extract title
        $title = $this->extractText($xpath, '//h1 | //h2 | //title');
        
        // Extract location from URL
        $location = $this->extractLocationFromUrl($url);
        
        // Extract coordinates from JavaScript data
        $coordinates = $this->extractCoordinates($dom);
        
        // Extract price
        $price = $this->extractPrice($xpath);
        
        // Extract description
        $description = $this->extractDescription($xpath);
        
        // Extract property type
        $propertyType = $this->extractPropertyType($title, $description);
        
        // Extract photos
        $photos = $this->extractPhotos($xpath);
        
        // Extract management company
        $managementCompany = $this->extractManagementCompany($xpath);
        
        // Extract amenities
        $amenities = $this->extractAmenities($xpath, $description);
        
        // Extract additional features
        $features = $this->extractFeatures($xpath);
        
        // Build property data array
        $propertyData = [
            'url' => $url,
            'title' => $title ?: 'N/A',
            'location' => $location ?: 'N/A',
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'status' => 'available',
            'price' => $price,
            'description' => $description,
            'property_type' => $propertyType,
            'available_date' => null,
            'photo_count' => count($photos),
            'first_photo_url' => !empty($photos) ? $photos[0] : null,
            'all_photos' => !empty($photos) ? implode(', ', $photos) : null,
            'photos' => !empty($photos) ? json_encode($photos) : null,
            'contact_info' => null,
            'management_company' => $managementCompany,
            'amenities' => !empty($amenities) ? implode(', ', $amenities) : null,
        ];
        
        // Add additional features
        $propertyData = array_merge($propertyData, $features);
        
        return $propertyData;
    }
    
    /**
     * Extract text from XPath
     */
    private function extractText(\DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $text = trim($nodes->item(0)->textContent);
            return !empty($text) ? $text : null;
        }
        return null;
    }
    
    /**
     * Extract location from URL
     */
    private function extractLocationFromUrl(string $url): ?string
    {
        if (preg_match('/\/london\/([^\/]+)\//', $url, $matches)) {
            return str_replace('_', ' ', ucfirst($matches[1]));
        }
        return null;
    }
    
    /**
     * Extract coordinates from HTML
     */
    private function extractCoordinates(\DOMDocument $dom): array
    {
        $latitude = null;
        $longitude = null;
        
        // Look for coordinates in script tags
        $scripts = $dom->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $content = $script->textContent;
            
            // Look for latitude and longitude patterns
            if (preg_match('/latitude["\s]*:["\s]*"?([0-9.-]+)"?/', $content, $latMatch)) {
                $latitude = (float) $latMatch[1];
            }
            if (preg_match('/longitude["\s]*:["\s]*"?([0-9.-]+)"?/', $content, $lonMatch)) {
                $longitude = (float) $lonMatch[1];
            }
            
            // Look for location object pattern
            if (preg_match('/location["\s]*:["\s]*{[^}]*latitude["\s]*:["\s]*"?([0-9.-]+)"?[^}]*longitude["\s]*:["\s]*"?([0-9.-]+)"?/', $content, $locMatch)) {
                $latitude = (float) $locMatch[1];
                $longitude = (float) $locMatch[2];
            }
        }
        
        // Validate coordinates
        if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
            $latitude = null;
        }
        if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
            $longitude = null;
        }
        
        return [
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
    }
    
    /**
     * Extract price from HTML
     */
    private function extractPrice(\DOMXPath $xpath): ?string
    {
        $priceSelectors = [
            '//*[contains(@class, "price")]',
            '//*[contains(@class, "rent")]',
            '//*[contains(@class, "amount")]',
            '//*[contains(text(), "£")]'
        ];
        
        foreach ($priceSelectors as $selector) {
            $nodes = $xpath->query($selector);
            foreach ($nodes as $node) {
                $text = trim($node->textContent);
                if (preg_match('/£?(\d+(?:,\d+)?(?:\.\d{2})?)/', $text, $matches)) {
                    return '£' . $matches[1];
                }
            }
        }
        
        return null;
    }
    
    /**
     * Extract description from HTML
     */
    private function extractDescription(\DOMXPath $xpath): ?string
    {
        $descSelectors = [
            '//div[contains(@class, "description")]',
            '//div[contains(@class, "details")]',
            '//div[contains(@class, "content")]',
            '//p[string-length(text()) > 50]'
        ];
        
        foreach ($descSelectors as $selector) {
            $nodes = $xpath->query($selector);
            foreach ($nodes as $node) {
                $text = trim($node->textContent);
                if (strlen($text) > 50 && strlen($text) < 1000) {
                    return $text;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Extract property type from title and description
     */
    private function extractPropertyType(?string $title, ?string $description): ?string
    {
        $searchText = strtolower(($title ?? '') . ' ' . ($description ?? ''));
        $typeKeywords = ['room', 'bedroom', 'studio', 'flat', 'apartment', 'house', 'en-suite', 'ensuite'];
        
        foreach ($typeKeywords as $keyword) {
            if (strpos($searchText, $keyword) !== false) {
                return ucfirst($keyword);
            }
        }
        
        return null;
    }
    
    /**
     * Extract photos from HTML
     */
    private function extractPhotos(\DOMXPath $xpath): array
    {
        $photos = [];
        $photoSelectors = [
            '//img[contains(@src, "photos2.spareroom.co.uk")]',
            '//img[contains(@src, "spareroom.co.uk")]'
        ];
        
        foreach ($photoSelectors as $selector) {
            $nodes = $xpath->query($selector);
            foreach ($nodes as $node) {
                $src = $node->getAttribute('src');
                if (!empty($src)) {
                    // Ensure it's a full URL
                    if (strpos($src, '//') === 0) {
                        $src = 'https:' . $src;
                    } elseif (strpos($src, '/') === 0) {
                        $src = 'https://photos2.spareroom.co.uk' . $src;
                    }
                    
                    if (filter_var($src, FILTER_VALIDATE_URL) && !in_array($src, $photos)) {
                        $photos[] = $src;
                    }
                }
            }
        }
        
        return $photos;
    }
    
    /**
     * Extract management company from HTML
     */
    private function extractManagementCompany(\DOMXPath $xpath): ?string
    {
        $companySelectors = [
            '//*[contains(@class, "profile-photo__name")]',
            '//*[contains(@class, "agent-name")]',
            '//*[contains(@class, "company-name")]'
        ];
        
        foreach ($companySelectors as $selector) {
            $nodes = $xpath->query($selector);
            if ($nodes->length > 0) {
                $text = trim($nodes->item(0)->textContent);
                if (strlen($text) > 2 && strlen($text) < 100) {
                    return $text;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Extract amenities from HTML
     */
    private function extractAmenities(\DOMXPath $xpath, ?string $description): array
    {
        $amenities = [];
        $searchText = strtolower(($description ?? ''));
        $amenityKeywords = ['wifi', 'internet', 'parking', 'garden', 'balcony', 'ensuite', 'bills included', 'furnished'];
        
        foreach ($amenityKeywords as $keyword) {
            if (strpos($searchText, $keyword) !== false) {
                $amenities[] = ucfirst($keyword);
            }
        }
        
        return $amenities;
    }
    
    /**
     * Extract additional features from HTML
     */
    private function extractFeatures(\DOMXPath $xpath): array
    {
        $features = [];
        
        // Extract features from structured sections
        $featureSections = [
            'feature--price_room_only' => ['price_pcm', 'room_type'],
            'feature--availability' => ['available_from', 'minimum_term'],
            'feature--extra-cost' => ['bills_included', 'deposit'],
            'feature--amenities' => ['furnishings', 'garden_patio'],
            'feature--current-household' => ['number_housemates', 'gender'],
            'feature--household-preferences' => ['pets_allowed', 'couples_allowed']
        ];
        
        foreach ($featureSections as $sectionClass => $fieldNames) {
            $section = $xpath->query("//section[contains(@class, '{$sectionClass}')]");
            if ($section->length > 0) {
                foreach ($fieldNames as $fieldName) {
                    $value = $this->extractFeatureValue($xpath, $section->item(0), $fieldName);
                    if ($value) {
                        $features[$fieldName] = $value;
                    }
                }
            }
        }
        
        return $features;
    }
    
    /**
     * Extract feature value from a section
     */
    private function extractFeatureValue(\DOMXPath $xpath, \DOMElement $section, string $fieldName): ?string
    {
        $keyNodes = $xpath->query(".//dt[contains(@class, 'feature-list__key')]", $section);
        $valueNodes = $xpath->query(".//dd[contains(@class, 'feature-list__value')]", $section);
        
        for ($i = 0; $i < min($keyNodes->length, $valueNodes->length); $i++) {
            $key = strtolower(trim($keyNodes->item($i)->textContent));
            $key = str_replace([' ', '?', '/'], ['_', '', '_'], $key);
            
            if ($key === $fieldName || strpos($key, $fieldName) !== false) {
                $value = trim($valueNodes->item($i)->textContent);
                return !empty($value) ? $value : null;
            }
        }
        
        return null;
    }
}
