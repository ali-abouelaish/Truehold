<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

echo "🔍 Debugging File Upload Issue\n";
echo "=============================\n\n";

try {
    // Test 1: Check current database state
    echo "1. Checking current database state...\n";
    $rentalCodes = RentalCode::whereNotNull('client_contract')
        ->orWhereNotNull('payment_proof')
        ->get();
    
    echo "Found {$rentalCodes->count()} rental codes with file data\n\n";
    
    foreach ($rentalCodes as $rentalCode) {
        echo "📋 Rental Code: {$rentalCode->rental_code}\n";
        
        if ($rentalCode->client_contract) {
            echo "   Client Contract: {$rentalCode->client_contract}\n";
            $contracts = json_decode($rentalCode->client_contract, true);
            if (is_array($contracts)) {
                echo "   Decoded contracts: " . print_r($contracts, true) . "\n";
            }
        }
        
        if ($rentalCode->payment_proof) {
            echo "   Payment Proof: {$rentalCode->payment_proof}\n";
            $proofs = json_decode($rentalCode->payment_proof, true);
            if (is_array($proofs)) {
                echo "   Decoded proofs: " . print_r($proofs, true) . "\n";
            }
        }
        
        echo "\n";
    }
    
    // Test 2: Check form structure
    echo "2. Checking form structure...\n";
    $editView = 'resources/views/admin/rental-codes/edit.blade.php';
    if (file_exists($editView)) {
        $content = file_get_contents($editView);
        
        // Check for file input fields
        $fileFields = [
            'client_contract' => 'name="client_contract[]"',
            'payment_proof' => 'name="payment_proof[]"',
            'client_id_document' => 'name="client_id_document[]"'
        ];
        
        foreach ($fileFields as $field => $pattern) {
            if (strpos($content, $pattern) !== false) {
                echo "   ✅ {$field} field found in form\n";
            } else {
                echo "   ❌ {$field} field missing in form\n";
            }
        }
        
        // Check for enctype
        if (strpos($content, 'enctype="multipart/form-data"') !== false) {
            echo "   ✅ Form enctype is correct\n";
        } else {
            echo "   ❌ Form enctype missing\n";
        }
        
        // Check for multiple attribute
        if (strpos($content, 'multiple') !== false) {
            echo "   ✅ Multiple file upload enabled\n";
        } else {
            echo "   ❌ Multiple file upload not enabled\n";
        }
    } else {
        echo "   ❌ Edit form file not found\n";
    }
    
    echo "\n";
    
    // Test 3: Check controller validation
    echo "3. Checking controller validation...\n";
    $controllerFile = 'app/Http/Controllers/RentalCodeController.php';
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        
        $validationChecks = [
            'client_contract validation' => "'client_contract.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'",
            'payment_proof validation' => "'payment_proof.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'",
            'client_id_document validation' => "'client_id_document.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'",
            'handleFileUploads method' => 'private function handleFileUploads(',
            'client contract processing' => 'Handle client contract uploads',
            'payment proof processing' => 'Handle payment proof uploads'
        ];
        
        foreach ($validationChecks as $check => $pattern) {
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
    
    // Test 4: Check storage permissions
    echo "4. Checking storage permissions...\n";
    $storagePath = storage_path('app/public');
    echo "   Storage path: {$storagePath}\n";
    
    if (is_dir($storagePath)) {
        echo "   ✅ Storage directory exists\n";
        
        if (is_writable($storagePath)) {
            echo "   ✅ Storage directory is writable\n";
        } else {
            echo "   ❌ Storage directory is not writable\n";
        }
        
        // Check rental-codes directory
        $rentalCodesDir = $storagePath . '/rental-codes';
        if (is_dir($rentalCodesDir)) {
            echo "   ✅ Rental codes directory exists\n";
            if (is_writable($rentalCodesDir)) {
                echo "   ✅ Rental codes directory is writable\n";
            } else {
                echo "   ❌ Rental codes directory is not writable\n";
            }
        } else {
            echo "   ❌ Rental codes directory missing\n";
            echo "   💡 Creating rental codes directory...\n";
            if (mkdir($rentalCodesDir, 0755, true)) {
                echo "   ✅ Created rental codes directory\n";
            } else {
                echo "   ❌ Failed to create rental codes directory\n";
            }
        }
    } else {
        echo "   ❌ Storage directory missing\n";
    }
    
    echo "\n";
    
    // Test 5: Test file upload simulation
    echo "5. Testing file upload simulation...\n";
    
    // Create a test file
    $testFile = storage_path('app/public/rental-codes/test-upload-' . time() . '.txt');
    $testContent = 'Test upload content at ' . now();
    
    if (file_put_contents($testFile, $testContent)) {
        echo "   ✅ Created test file: {$testFile}\n";
        
        if (file_exists($testFile)) {
            echo "   ✅ Test file exists and is readable\n";
            
            // Test JSON encoding
            $testPaths = [str_replace(storage_path('app/public/'), '', $testFile)];
            $jsonEncoded = json_encode($testPaths);
            echo "   JSON encoded: {$jsonEncoded}\n";
            
            // Clean up
            unlink($testFile);
            echo "   🧹 Cleaned up test file\n";
        } else {
            echo "   ❌ Test file not found after creation\n";
        }
    } else {
        echo "   ❌ Failed to create test file\n";
    }
    
    echo "\n";
    
    // Test 6: Check Laravel logs
    echo "6. Checking Laravel logs...\n";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $logSize = filesize($logFile);
        echo "   Log file size: " . number_format($logSize) . " bytes\n";
        
        if (strpos($logContent, 'handleFileUploads') !== false) {
            echo "   ✅ File upload logs found\n";
        } else {
            echo "   ⚠️  No file upload logs found\n";
        }
        
        if (strpos($logContent, 'Processing client contract uploads') !== false) {
            echo "   ✅ Client contract processing logs found\n";
        } else {
            echo "   ⚠️  No client contract processing logs found\n";
        }
        
        if (strpos($logContent, 'Processing payment proof uploads') !== false) {
            echo "   ✅ Payment proof processing logs found\n";
        } else {
            echo "   ⚠️  No payment proof processing logs found\n";
        }
    } else {
        echo "   ❌ Log file not found\n";
    }
    
    echo "\n🎉 Debug analysis completed!\n";
    echo "\n💡 Common causes of upload failures:\n";
    echo "1. Form not sending files properly\n";
    echo "2. Files not being validated correctly\n";
    echo "3. Storage permissions issues\n";
    echo "4. Controller not processing files correctly\n";
    echo "5. JavaScript preventing file upload\n";
    echo "6. File size or type restrictions\n";
    echo "\n🔧 Next steps:\n";
    echo "1. Check browser network tab when uploading\n";
    echo "2. Verify files are being sent in the request\n";
    echo "3. Check Laravel logs for detailed errors\n";
    echo "4. Test with a simple file upload\n";
    echo "5. Verify storage permissions\n";

} catch (Exception $e) {
    echo "❌ Debug failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
