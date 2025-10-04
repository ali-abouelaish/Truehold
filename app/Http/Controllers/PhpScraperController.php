<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PhpScraperController extends Controller
{
    public function runPhpScraper()
    {
        try {
            // Read profiles from CSV
            $profiles = $this->readProfilesCsv();
            
            if (empty($profiles)) {
                return redirect()->route('admin.scraper.index')->with('error', 'No profile URLs found. Please add some profiles first.');
            }
            
            $allListings = [];
            $processedCount = 0;
            
            foreach ($profiles as $profileUrl) {
                try {
                    Log::info("Processing profile: $profileUrl");
                    
                    // Get the profile page
                    $response = Http::timeout(30)->get($profileUrl);
                    
                    if (!$response->successful()) {
                        Log::warning("Failed to fetch profile: $profileUrl - Status: " . $response->status());
                        continue;
                    }
                    
                    $html = $response->body();
                    
                    // Extract listing URLs using regex (simplified approach)
                    preg_match_all('/href="([^"]*\/rooms\/[^"]*)"/', $html, $matches);
                    $listingUrls = array_unique($matches[1]);
                    
                    Log::info("Found " . count($listingUrls) . " listings in profile: $profileUrl");
                    
                    // Process each listing
                    foreach ($listingUrls as $listingUrl) {
                        if (strpos($listingUrl, 'http') !== 0) {
                            $listingUrl = 'https://www.spareroom.co.uk' . $listingUrl;
                        }
                        
                        try {
                            $listingData = $this->scrapeListing($listingUrl);
                            if ($listingData) {
                                $allListings[] = $listingData;
                                $processedCount++;
                            }
                        } catch (\Exception $e) {
                            Log::warning("Failed to scrape listing: $listingUrl - " . $e->getMessage());
                        }
                        
                        // Add delay to avoid being blocked
                        usleep(500000); // 0.5 seconds
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Error processing profile $profileUrl: " . $e->getMessage());
                }
            }
            
            // Save to CSV
            $this->saveToCsv($allListings);
            
            return redirect()->route('admin.scraper.index')->with('success', "PHP Scraper completed! Processed $processedCount listings from " . count($profiles) . " profiles.");
            
        } catch (\Exception $e) {
            Log::error("PHP Scraper error: " . $e->getMessage());
            return redirect()->route('admin.scraper.index')->with('error', 'PHP Scraper failed: ' . $e->getMessage());
        }
    }
    
    private function scrapeListing($url)
    {
        try {
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                return null;
            }
            
            $html = $response->body();
            
            // Extract data using regex patterns
            $data = [
                'url' => $url,
                'title' => $this->extractText($html, '/<h1[^>]*class="[^"]*room-title[^"]*"[^>]*>(.*?)<\/h1>/s'),
                'price' => $this->extractText($html, '/<span[^>]*class="[^"]*price[^"]*"[^>]*>(.*?)<\/span>/s'),
                'description' => $this->extractText($html, '/<div[^>]*class="[^"]*description[^"]*"[^>]*>(.*?)<\/div>/s'),
                'location' => $this->extractText($html, '/<span[^>]*class="[^"]*location[^"]*"[^>]*>(.*?)<\/span>/s'),
                'property_type' => $this->extractText($html, '/<span[^>]*class="[^"]*property-type[^"]*"[^>]*>(.*?)<\/span>/s'),
                'available_from' => $this->extractText($html, '/<span[^>]*class="[^"]*available-from[^"]*"[^>]*>(.*?)<\/span>/s'),
                'minimum_stay' => $this->extractText($html, '/<span[^>]*class="[^"]*minimum-stay[^"]*"[^>]*>(.*?)<\/span>/s'),
                'maximum_stay' => $this->extractText($html, '/<span[^>]*class="[^"]*maximum-stay[^"]*"[^>]*>(.*?)<\/span>/s'),
                'room_type' => $this->extractText($html, '/<span[^>]*class="[^"]*room-type[^"]*"[^>]*>(.*?)<\/span>/s'),
                'furnished' => $this->extractText($html, '/<span[^>]*class="[^"]*furnished[^"]*"[^>]*>(.*?)<\/span>/s'),
                'bills_included' => $this->extractText($html, '/<span[^>]*class="[^"]*bills-included[^"]*"[^>]*>(.*?)<\/span>/s'),
                'deposit' => $this->extractText($html, '/<span[^>]*class="[^"]*deposit[^"]*"[^>]*>(.*?)<\/span>/s'),
                'contact_name' => $this->extractText($html, '/<span[^>]*class="[^"]*contact-name[^"]*"[^>]*>(.*?)<\/span>/s'),
                'contact_phone' => $this->extractText($html, '/<span[^>]*class="[^"]*contact-phone[^"]*"[^>]*>(.*?)<\/span>/s'),
                'contact_email' => $this->extractText($html, '/<span[^>]*class="[^"]*contact-email[^"]*"[^>]*>(.*?)<\/span>/s'),
                'images' => $this->extractImages($html),
                'scraped_at' => now()->toDateTimeString(),
            ];
            
            // Clean up the data
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = trim(strip_tags($value));
                }
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::warning("Error scraping listing $url: " . $e->getMessage());
            return null;
        }
    }
    
    private function extractText($html, $pattern)
    {
        if (preg_match($pattern, $html, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }
    
    private function extractImages($html)
    {
        preg_match_all('/<img[^>]*src="([^"]*)"[^>]*>/i', $html, $matches);
        return implode(',', array_filter($matches[1]));
    }
    
    private function readProfilesCsv()
    {
        if (!Storage::disk('local')->exists('profiles.csv')) {
            return [];
        }
        
        $contents = Storage::disk('local')->get('profiles.csv');
        return array_filter(array_map('trim', explode("\n", $contents)));
    }
    
    private function saveToCsv($listings)
    {
        if (empty($listings)) {
            return;
        }
        
        $csvContent = '';
        
        // Add header
        $headers = array_keys($listings[0]);
        $csvContent .= implode(',', $headers) . "\n";
        
        // Add data rows
        foreach ($listings as $listing) {
            $row = [];
            foreach ($headers as $header) {
                $value = $listing[$header] ?? '';
                // Escape CSV values
                $value = str_replace('"', '""', $value);
                $row[] = '"' . $value . '"';
            }
            $csvContent .= implode(',', $row) . "\n";
        }
        
        Storage::disk('local')->put('newscrape.csv', $csvContent);
    }
}
