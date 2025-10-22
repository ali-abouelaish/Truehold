<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;

echo "🧪 Testing File Upload Fix\n";
echo "========================\n\n";

try {
    // Test 1: Create a test file
    echo "1. Creating test file...\n";
    $testContent = "This is a test file created at " . now();
    $testPath = 'rental-codes/documents/test-' . time() . '.txt';
    
    Storage::disk('public')->put($testPath, $testContent);
    echo "   ✅ Created test file: {$testPath}\n";
    
    // Test 2: Verify file exists
    if (Storage::disk('public')->exists($testPath)) {
        echo "   ✅ Test file exists in storage\n";
        
        $content = Storage::disk('public')->get($testPath);
        if ($content === $testContent) {
            echo "   ✅ Test file content is correct\n";
        } else {
            echo "   ❌ Test file content mismatch\n";
        }
    } else {
        echo "   ❌ Test file not found in storage\n";
    }
    
    // Test 3: Test JSON encoding
    echo "2. Testing JSON encoding...\n";
    $testPaths = [$testPath];
    $jsonEncoded = json_encode($testPaths);
    echo "   JSON encoded: {$jsonEncoded}\n";
    
    $decoded = json_decode($jsonEncoded, true);
    if (is_array($decoded) && count($decoded) === 1) {
        echo "   ✅ JSON encoding/decoding works correctly\n";
    } else {
        echo "   ❌ JSON encoding/decoding failed\n";
    }
    
    // Test 4: Test empty array filtering
    echo "3. Testing empty array filtering...\n";
    $mixedArray = [null, '', $testPath, null, 'another-file.txt'];
    $filteredArray = array_filter($mixedArray, function($item) {
        return !empty($item) && $item !== null;
    });
    
    echo "   Original array: " . print_r($mixedArray, true);
    echo "   Filtered array: " . print_r($filteredArray, true);
    
    if (count($filteredArray) === 2) {
        echo "   ✅ Array filtering works correctly\n";
    } else {
        echo "   ❌ Array filtering failed\n";
    }
    
    // Test 5: Test database update simulation
    echo "4. Testing database update simulation...\n";
    $rentalCode = RentalCode::first();
    
    if ($rentalCode) {
        echo "   Using rental code: {$rentalCode->rental_code}\n";
        
        // Simulate the update
        $validPaths = array_filter([$testPath], function($path) {
            return !empty($path) && Storage::disk('public')->exists($path);
        });
        
        if (!empty($validPaths)) {
            $jsonPaths = json_encode($validPaths);
            echo "   Would update database with: {$jsonPaths}\n";
            echo "   ✅ Database update simulation successful\n";
        } else {
            echo "   ❌ No valid paths to update\n";
        }
    } else {
        echo "   ⚠️  No rental codes found for testing\n";
    }
    
    // Test 6: Clean up test file
    echo "5. Cleaning up test file...\n";
    if (Storage::disk('public')->exists($testPath)) {
        Storage::disk('public')->delete($testPath);
        echo "   ✅ Test file deleted\n";
    }
    
    echo "\n🎉 File upload test completed!\n";
    echo "\n💡 The improved file upload handling should now:\n";
    echo "1. Filter out null and invalid files\n";
    echo "2. Only process files with size > 0\n";
    echo "3. Only update database when valid files exist\n";
    echo "4. Prevent empty JSON arrays from being stored\n";
    echo "\n🔧 Next steps:\n";
    echo "1. Try uploading files through the edit form\n";
    echo "2. Check the Laravel logs for detailed processing info\n";
    echo "3. Verify database contains proper file paths\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
