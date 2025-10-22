<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;

echo "🧪 Testing New Upload System\n";
echo "===========================\n\n";

try {
    // Test 1: Check if rental codes exist
    echo "1. Checking rental codes...\n";
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

    // Test 4: Test database update simulation
    echo "4. Testing database update simulation...\n";
    
    $validPaths = $testPaths;
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

    // Test 5: Check controller methods
    echo "5. Checking controller methods...\n";
    $controllerFile = 'app/Http/Controllers/RentalCodeController.php';
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        
        $methodChecks = [
            'handleFileUploads method' => 'private function handleFileUploads(',
            'processFileUploads method' => 'private function processFileUploads(',
            'client contract processing' => 'Process client contracts',
            'payment proof processing' => 'Process payment proofs',
            'client ID document processing' => 'Process client ID documents'
        ];
        
        foreach ($methodChecks as $check => $pattern) {
            if (strpos($content, $pattern) !== false) {
                echo "   ✅ {$check}\n";
            } else {
                echo "   ❌ {$check}\n";
            }
        }
    } else {
        echo "   ❌ Controller file not found\n";
    }
    
    echo "\n";

    // Test 6: Check form structure
    echo "6. Checking form structure...\n";
    $createView = 'resources/views/admin/rental-codes/create.blade.php';
    $editView = 'resources/views/admin/rental-codes/edit.blade.php';
    
    $views = [
        'Create form' => $createView,
        'Edit form' => $editView
    ];
    
    foreach ($views as $name => $view) {
        if (file_exists($view)) {
            $content = file_get_contents($view);
            
            $formChecks = [
                'enctype multipart' => 'enctype="multipart/form-data"',
                'client_contract field' => 'name="client_contract[]"',
                'payment_proof field' => 'name="payment_proof[]"',
                'client_id_document field' => 'name="client_id_document[]"',
                'multiple attribute' => 'multiple',
                'file input type' => 'type="file"'
            ];
            
            echo "   📋 {$name}:\n";
            foreach ($formChecks as $check => $pattern) {
                if (strpos($content, $pattern) !== false) {
                    echo "     ✅ {$check}\n";
                } else {
                    echo "     ❌ {$check}\n";
                }
            }
        } else {
            echo "   ❌ {$name} not found\n";
        }
    }
    
    echo "\n🎉 New upload system test completed!\n";
    echo "\n💡 Key improvements in the new system:\n";
    echo "1. Simplified file processing logic\n";
    echo "2. Better error handling and logging\n";
    echo "3. Cleaner form design with helpful tips\n";
    echo "4. More reliable file validation\n";
    echo "5. Consistent handling across create and edit\n";
    echo "\n🔧 The new system should:\n";
    echo "1. Process files more reliably\n";
    echo "2. Provide better error messages\n";
    echo "3. Handle multiple files correctly\n";
    echo "4. Store files in the right location\n";
    echo "5. Update the database properly\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
