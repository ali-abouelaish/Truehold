<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;

echo "🔍 Debugging File Upload Issue\n";
echo "============================\n\n";

try {
    // Test 1: Check current database state
    echo "1. Checking current database state...\n";
    $rentalCodes = RentalCode::whereNotNull('client_contract')
        ->orWhereNotNull('payment_proof')
        ->orWhereNotNull('client_id_document')
        ->get();
    
    foreach ($rentalCodes as $rentalCode) {
        echo "📋 Rental Code: {$rentalCode->rental_code}\n";
        
        if ($rentalCode->client_contract) {
            echo "   Client Contract: {$rentalCode->client_contract}\n";
            $contracts = json_decode($rentalCode->client_contract, true);
            if (is_array($contracts)) {
                echo "   Decoded: " . print_r($contracts, true) . "\n";
            }
        }
        
        if ($rentalCode->payment_proof) {
            echo "   Payment Proof: {$rentalCode->payment_proof}\n";
        }
        
        if ($rentalCode->client_id_document) {
            echo "   Client ID Doc: {$rentalCode->client_id_document}\n";
        }
        
        echo "\n";
    }
    
    // Test 2: Check storage directory
    echo "2. Checking storage directory...\n";
    $storagePath = storage_path('app/public');
    echo "   Storage path: {$storagePath}\n";
    
    if (is_dir($storagePath)) {
        echo "   ✅ Storage directory exists\n";
        
        // Check rental-codes directory
        $rentalCodesDir = $storagePath . '/rental-codes';
        if (is_dir($rentalCodesDir)) {
            echo "   ✅ Rental codes directory exists\n";
            $files = scandir($rentalCodesDir);
            $fileCount = count($files) - 2; // Subtract . and ..
            echo "   📁 Files in rental-codes: {$fileCount}\n";
            
            if ($fileCount > 0) {
                echo "   Files found:\n";
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        echo "     - {$file}\n";
                    }
                }
            }
        } else {
            echo "   ❌ Rental codes directory missing\n";
        }
    } else {
        echo "   ❌ Storage directory missing\n";
    }
    
    // Test 3: Check symbolic link
    echo "3. Checking symbolic link...\n";
    $publicStorage = public_path('storage');
    if (is_link($publicStorage)) {
        echo "   ✅ Storage symbolic link exists\n";
        $target = readlink($publicStorage);
        echo "   🔗 Links to: {$target}\n";
    } else {
        echo "   ❌ Storage symbolic link missing\n";
        echo "   💡 Run: php artisan storage:link\n";
    }
    
    // Test 4: Test file upload simulation
    echo "4. Testing file upload simulation...\n";
    
    // Create a test file
    $testFile = storage_path('app/public/test-upload.txt');
    file_put_contents($testFile, 'Test upload content');
    echo "   ✅ Created test file: {$testFile}\n";
    
    // Test if we can read it
    if (file_exists($testFile)) {
        echo "   ✅ Test file exists and is readable\n";
        unlink($testFile); // Clean up
        echo "   🧹 Cleaned up test file\n";
    } else {
        echo "   ❌ Test file not found\n";
    }
    
    // Test 5: Check form structure
    echo "5. Checking form structure...\n";
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
    } else {
        echo "   ❌ Edit form file not found\n";
    }
    
    echo "\n🎉 Debug analysis completed!\n";
    echo "\n💡 Common causes of empty JSON arrays:\n";
    echo "1. Form not sending files properly\n";
    echo "2. Files not being validated correctly\n";
    echo "3. Storage permissions issues\n";
    echo "4. JavaScript preventing file upload\n";
    echo "5. Controller not processing files correctly\n";
    echo "\n🔧 Next steps:\n";
    echo "1. Check browser network tab when uploading\n";
    echo "2. Verify files are being sent in the request\n";
    echo "3. Check Laravel logs for errors\n";
    echo "4. Test with a simple file upload\n";

} catch (Exception $e) {
    echo "❌ Debug failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
