<?php
// Test the PHP scraper functionality
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

echo "<h2>Testing PHP Scraper</h2>";

// Test reading profiles.csv
if (file_exists('profiles.csv')) {
    $profiles = array_filter(array_map('trim', file('profiles.csv')));
    echo "<p>✅ Found " . count($profiles) . " profiles in profiles.csv</p>";
    
    foreach ($profiles as $profile) {
        echo "<p>Profile: $profile</p>";
    }
} else {
    echo "<p>❌ profiles.csv not found</p>";
}

// Test HTTP client
echo "<h3>Testing HTTP Client</h3>";
try {
    $response = Http::timeout(10)->get('https://httpbin.org/get');
    if ($response->successful()) {
        echo "<p>✅ HTTP client working - Status: " . $response->status() . "</p>";
    } else {
        echo "<p>❌ HTTP client failed - Status: " . $response->status() . "</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ HTTP client error: " . $e->getMessage() . "</p>";
}

// Test profile URL (if available)
if (isset($profiles[0])) {
    $profileUrl = $profiles[0];
    echo "<h3>Testing Profile URL: $profileUrl</h3>";
    
    try {
        $response = Http::timeout(30)->get($profileUrl);
        if ($response->successful()) {
            echo "<p>✅ Profile accessible - Status: " . $response->status() . "</p>";
            echo "<p>Content length: " . strlen($response->body()) . " characters</p>";
            
            // Look for listing URLs
            preg_match_all('/href="([^"]*\/rooms\/[^"]*)"/', $response->body(), $matches);
            $listingUrls = array_unique($matches[1]);
            echo "<p>Found " . count($listingUrls) . " potential listing URLs</p>";
            
            if (count($listingUrls) > 0) {
                echo "<p>First few listing URLs:</p><ul>";
                foreach (array_slice($listingUrls, 0, 3) as $url) {
                    if (strpos($url, 'http') !== 0) {
                        $url = 'https://www.spareroom.co.uk' . $url;
                    }
                    echo "<li><a href='$url' target='_blank'>$url</a></li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p>❌ Profile not accessible - Status: " . $response->status() . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Profile access error: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>PHP Scraper Test Complete</h3>";
echo "<p>If all tests pass, the PHP scraper should work in your Laravel application.</p>";
?>
