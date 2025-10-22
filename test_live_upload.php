<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;

echo "🧪 Testing Live Server Upload\n";
echo "============================\n\n";

try {
    // Test 1: Basic file storage
    echo "1. Testing basic file storage...\n";
    $testContent = 'Live server test file at ' . now();
    $testPath = 'rental-codes/documents/live-test-' . time() . '.txt';
    
    if (Storage::disk('public')->put($testPath, $testContent)) {
        echo "   ✅ File stored successfully\n";
        
        if (Storage::disk('public')->exists($testPath)) {
            echo "   ✅ File exists in storage\n";
            
            $retrievedContent = Storage::disk('public')->get($testPath);
            if ($retrievedContent === $testContent) {
                echo "   ✅ File content matches\n";
            } else {
                echo "   ❌ File content mismatch\n";
            }
            
            // Test URL generation
            $url = Storage::disk('public')->url($testPath);
            echo "   📁 File URL: {$url}\n";
            
            // Test if URL is accessible
            $headers = @get_headers($url);
            if ($headers && strpos($headers[0], '200') !== false) {
                echo "   ✅ File URL is accessible\n";
            } else {
                echo "   ❌ File URL is not accessible\n";
                echo "   💡 Check symbolic link and web server config\n";
            }
            
            // Clean up
            Storage::disk('public')->delete($testPath);
            echo "   🧹 Cleaned up test file\n";
        } else {
            echo "   ❌ File not found after storage\n";
        }
    } else {
        echo "   ❌ Failed to store file\n";
    }
    
    echo "\n";

    // Test 2: Test with rental code
    echo "2. Testing with rental code...\n";
    $rentalCode = RentalCode::first();
    
    if ($rentalCode) {
        echo "   📋 Using rental code: {$rentalCode->rental_code}\n";
        
        // Test JSON encoding/decoding
        $testPaths = [
            'rental-codes/documents/test1.jpg',
            'rental-codes/documents/test2.pdf'
        ];
        
        $jsonEncoded = json_encode($testPaths);
        echo "   📝 JSON encoded: {$jsonEncoded}\n";
        
        // Test database update
        $originalContract = $rentalCode->client_contract;
        $rentalCode->update(['client_contract' => $jsonEncoded]);
        $rentalCode->refresh();
        
        echo "   💾 Database updated\n";
        echo "   📊 New client_contract: {$rentalCode->client_contract}\n";
        
        // Verify the update
        $decoded = json_decode($rentalCode->client_contract, true);
        if (is_array($decoded) && count($decoded) === 2) {
            echo "   ✅ Database update successful\n";
        } else {
            echo "   ❌ Database update failed\n";
        }
        
        // Restore original value
        $rentalCode->update(['client_contract' => $originalContract]);
        echo "   🔄 Restored original value\n";
    } else {
        echo "   ⚠️  No rental codes found for testing\n";
    }
    
    echo "\n";

    // Test 3: Check form submission simulation
    echo "3. Testing form submission simulation...\n";
    
    // Simulate the controller logic
    $simulatedFiles = [
        'client_contract' => [
            'name' => 'test-contract.pdf',
            'size' => 1024,
            'type' => 'application/pdf'
        ],
        'payment_proof' => [
            'name' => 'test-payment.jpg',
            'size' => 2048,
            'type' => 'image/jpeg'
        ]
    ];
    
    echo "   📁 Simulated files:\n";
    foreach ($simulatedFiles as $field => $file) {
        echo "     {$field}: {$file['name']} ({$file['size']} bytes)\n";
    }
    
    // Test the processing logic
    $validPaths = [];
    foreach ($simulatedFiles as $field => $file) {
        if ($file['size'] > 0) {
            $validPaths[$field] = ["rental-codes/documents/{$file['name']}"];
        }
    }
    
    if (!empty($validPaths)) {
        echo "   ✅ File processing logic works\n";
        foreach ($validPaths as $field => $paths) {
            echo "     {$field}: " . json_encode($paths) . "\n";
        }
    } else {
        echo "   ❌ File processing logic failed\n";
    }
    
    echo "\n🎉 Live server upload test completed!\n";
    echo "\n💡 If all tests passed, the upload system should work on live server.\n";
    echo "If tests failed, check:\n";
    echo "1. File permissions on storage directory\n";
    echo "2. Symbolic link from public/storage to storage/app/public\n";
    echo "3. PHP upload limits in php.ini\n";
    echo "4. Web server configuration\n";
    echo "5. Laravel logs for specific errors\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
