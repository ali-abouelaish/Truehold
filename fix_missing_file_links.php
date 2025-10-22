<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;

echo "🔧 Fixing Missing File Links in Database\n";
echo "======================================\n\n";

try {
    // Check rental codes with missing file links
    echo "1. Checking rental codes for missing file links...\n";
    $rentalCodes = RentalCode::all();
    
    $fixedCount = 0;
    $missingCount = 0;
    
    foreach ($rentalCodes as $rentalCode) {
        echo "\n📋 Rental Code: {$rentalCode->rental_code}\n";
        
        // Check client_contract field
        if ($rentalCode->client_contract) {
            $contracts = json_decode($rentalCode->client_contract, true);
            if (is_array($contracts)) {
                echo "   ✅ Client contracts: " . count($contracts) . " files\n";
                foreach ($contracts as $contract) {
                    if (Storage::disk('public')->exists($contract)) {
                        echo "     ✅ {$contract}\n";
                    } else {
                        echo "     ❌ {$contract} (file not found)\n";
                    }
                }
            } else {
                echo "   ⚠️  Client contracts: Invalid JSON format\n";
            }
        } else {
            echo "   ⚠️  Client contracts: No data\n";
        }
        
        // Check payment_proof field
        if ($rentalCode->payment_proof) {
            $proofs = json_decode($rentalCode->payment_proof, true);
            if (is_array($proofs)) {
                echo "   ✅ Payment proofs: " . count($proofs) . " files\n";
                foreach ($proofs as $proof) {
                    if (Storage::disk('public')->exists($proof)) {
                        echo "     ✅ {$proof}\n";
                    } else {
                        echo "     ❌ {$proof} (file not found)\n";
                    }
                }
            } else {
                echo "   ⚠️  Payment proofs: Invalid JSON format\n";
            }
        } else {
            echo "   ⚠️  Payment proofs: No data\n";
        }
        
        // Check client_id_document field
        if ($rentalCode->client_id_document) {
            $ids = json_decode($rentalCode->client_id_document, true);
            if (is_array($ids)) {
                echo "   ✅ Client ID documents: " . count($ids) . " files\n";
                foreach ($ids as $id) {
                    if (Storage::disk('public')->exists($id)) {
                        echo "     ✅ {$id}\n";
                    } else {
                        echo "     ❌ {$id} (file not found)\n";
                    }
                }
            } else {
                echo "   ⚠️  Client ID documents: Invalid JSON format\n";
            }
        } else {
            echo "   ⚠️  Client ID documents: No data\n";
        }
        
        // Check cash document fields
        if ($rentalCode->contact_images) {
            $images = json_decode($rentalCode->contact_images, true);
            if (is_array($images)) {
                echo "   ✅ Contact images: " . count($images) . " files\n";
                foreach ($images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        echo "     ✅ {$image}\n";
                    } else {
                        echo "     ❌ {$image} (file not found)\n";
                    }
                }
            } else {
                echo "   ⚠️  Contact images: Invalid JSON format\n";
            }
        } else {
            echo "   ⚠️  Contact images: No data\n";
        }
        
        if ($rentalCode->client_id_image) {
            if (Storage::disk('public')->exists($rentalCode->client_id_image)) {
                echo "   ✅ Client ID image: {$rentalCode->client_id_image}\n";
            } else {
                echo "   ❌ Client ID image: {$rentalCode->client_id_image} (file not found)\n";
            }
        } else {
            echo "   ⚠️  Client ID image: No data\n";
        }
        
        if ($rentalCode->cash_receipt_image) {
            if (Storage::disk('public')->exists($rentalCode->cash_receipt_image)) {
                echo "   ✅ Cash receipt image: {$rentalCode->cash_receipt_image}\n";
            } else {
                echo "   ❌ Cash receipt image: {$rentalCode->cash_receipt_image} (file not found)\n";
            }
        } else {
            echo "   ⚠️  Cash receipt image: No data\n";
        }
    }
    
    echo "\n2. Checking storage directory structure...\n";
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
        } else {
            echo "   ❌ Rental codes directory missing\n";
        }
        
        // Check cash-documents directory
        $cashDocsDir = $storagePath . '/cash-documents';
        if (is_dir($cashDocsDir)) {
            echo "   ✅ Cash documents directory exists\n";
            $files = scandir($cashDocsDir);
            $fileCount = count($files) - 2; // Subtract . and ..
            echo "   📁 Files in cash-documents: {$fileCount}\n";
        } else {
            echo "   ❌ Cash documents directory missing\n";
        }
    } else {
        echo "   ❌ Storage directory missing\n";
    }
    
    echo "\n3. Checking symbolic link...\n";
    $publicStorage = public_path('storage');
    if (is_link($publicStorage)) {
        echo "   ✅ Storage symbolic link exists\n";
        $target = readlink($publicStorage);
        echo "   🔗 Links to: {$target}\n";
    } else {
        echo "   ❌ Storage symbolic link missing\n";
        echo "   💡 Run: php artisan storage:link\n";
    }
    
    echo "\n4. Testing file access...\n";
    $testFiles = [
        'rental-codes/documents/34NgcYKWAdJhJ1DLoEWlJWZkYBSDio4602Kmr37K.jpg',
        'cash-documents/contact-images/3HJgNyQXTKHLOwOfWnvc4TT8v3R4zMkmtT9FZa5Q.jpg'
    ];
    
    foreach ($testFiles as $testFile) {
        if (Storage::disk('public')->exists($testFile)) {
            echo "   ✅ {$testFile} - accessible\n";
        } else {
            echo "   ❌ {$testFile} - not accessible\n";
        }
    }
    
    echo "\n🎉 File link analysis completed!\n";
    echo "\n💡 If files are missing from database but exist in storage:\n";
    echo "   1. The files were uploaded but not saved to database\n";
    echo "   2. Try uploading the files again through the edit form\n";
    echo "   3. The update method now handles file uploads properly\n";

} catch (Exception $e) {
    echo "❌ Analysis failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
