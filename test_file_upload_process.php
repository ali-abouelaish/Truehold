<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;

echo "🧪 Testing File Upload Process\n";
echo "=============================\n\n";

try {
    // Test 1: Get a rental code to test with
    echo "1. Getting rental code for testing...\n";
    $rentalCode = RentalCode::first();
    
    if (!$rentalCode) {
        echo "❌ No rental codes found. Please create a rental code first.\n";
        exit(1);
    }
    
    echo "✅ Found rental code: {$rentalCode->rental_code}\n";
    echo "   ID: {$rentalCode->id}\n\n";
    
    // Test 2: Test file storage
    echo "2. Testing file storage...\n";
    $testContent = 'Test file content at ' . now();
    $testPath = 'rental-codes/documents/test-' . time() . '.txt';
    
    if (Storage::disk('public')->put($testPath, $testContent)) {
        echo "   ✅ Successfully stored test file: {$testPath}\n";
        
        if (Storage::disk('public')->exists($testPath)) {
            echo "   ✅ Test file exists in storage\n";
            
            $retrievedContent = Storage::disk('public')->get($testPath);
            if ($retrievedContent === $testContent) {
                echo "   ✅ Test file content matches\n";
            } else {
                echo "   ❌ Test file content mismatch\n";
            }
            
            // Clean up
            Storage::disk('public')->delete($testPath);
            echo "   🧹 Cleaned up test file\n";
        } else {
            echo "   ❌ Test file not found in storage\n";
        }
    } else {
        echo "   ❌ Failed to store test file\n";
    }
    
    echo "\n";
    
    // Test 3: Test JSON encoding/decoding
    echo "3. Testing JSON encoding/decoding...\n";
    $testPaths = [
        'rental-codes/documents/file1.jpg',
        'rental-codes/documents/file2.pdf'
    ];
    
    $jsonEncoded = json_encode($testPaths);
    echo "   Original array: " . print_r($testPaths, true);
    echo "   JSON encoded: {$jsonEncoded}\n";
    
    $decoded = json_decode($jsonEncoded, true);
    if (is_array($decoded) && count($decoded) === 2) {
        echo "   ✅ JSON encoding/decoding works correctly\n";
    } else {
        echo "   ❌ JSON encoding/decoding failed\n";
    }
    
    echo "\n";
    
    // Test 4: Test file validation logic
    echo "4. Testing file validation logic...\n";
    
    // Simulate the validation logic from the controller
    $testFiles = [
        null,
        '',
        'valid-file.jpg',
        null,
        'another-file.pdf'
    ];
    
    echo "   Test files: " . print_r($testFiles, true);
    
    $validFiles = array_filter($testFiles, function($file) {
        return $file && is_string($file) && !empty($file);
    });
    
    echo "   Valid files: " . print_r($validFiles, true);
    
    if (count($validFiles) === 2) {
        echo "   ✅ File validation logic works correctly\n";
    } else {
        echo "   ❌ File validation logic failed\n";
    }
    
    echo "\n";
    
    // Test 5: Test database update simulation
    echo "5. Testing database update simulation...\n";
    
    $validPaths = array_values($validFiles);
    $jsonPaths = json_encode($validPaths);
    
    echo "   Valid paths: " . print_r($validPaths, true);
    echo "   JSON paths: {$jsonPaths}\n";
    
    // Simulate the database update
    $originalContract = $rentalCode->client_contract;
    echo "   Original client_contract: {$originalContract}\n";
    
    // Update the rental code
    $rentalCode->update(['client_contract' => $jsonPaths]);
    $rentalCode->refresh();
    
    echo "   Updated client_contract: {$rentalCode->client_contract}\n";
    
    // Verify the update
    $decodedContract = json_decode($rentalCode->client_contract, true);
    if (is_array($decodedContract) && count($decodedContract) === 2) {
        echo "   ✅ Database update successful\n";
    } else {
        echo "   ❌ Database update failed\n";
    }
    
    // Restore original value
    $rentalCode->update(['client_contract' => $originalContract]);
    echo "   🔄 Restored original client_contract value\n";
    
    echo "\n";
    
    // Test 6: Check controller method exists
    echo "6. Checking controller method...\n";
    $controllerFile = 'app/Http/Controllers/RentalCodeController.php';
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        
        if (strpos($content, 'private function handleFileUploads(') !== false) {
            echo "   ✅ handleFileUploads method exists\n";
        } else {
            echo "   ❌ handleFileUploads method missing\n";
        }
        
        if (strpos($content, 'Handle client contract uploads') !== false) {
            echo "   ✅ Client contract processing exists\n";
        } else {
            echo "   ❌ Client contract processing missing\n";
        }
        
        if (strpos($content, 'Handle payment proof uploads') !== false) {
            echo "   ✅ Payment proof processing exists\n";
        } else {
            echo "   ❌ Payment proof processing missing\n";
        }
    } else {
        echo "   ❌ Controller file not found\n";
    }
    
    echo "\n🎉 File upload process test completed!\n";
    echo "\n💡 If uploads are still not working:\n";
    echo "1. Check browser developer tools (F12) -> Network tab\n";
    echo "2. Look for failed requests or 500 errors\n";
    echo "3. Check Laravel logs: tail -f storage/logs/laravel.log\n";
    echo "4. Verify file permissions on storage directory\n";
    echo "5. Test with a simple file (small size, common format)\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
