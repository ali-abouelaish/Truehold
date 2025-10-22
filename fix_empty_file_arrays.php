<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;

echo "🔧 Fixing Empty File Arrays in Database\n";
echo "====================================\n\n";

try {
    // Find rental codes with empty file arrays
    echo "1. Checking for rental codes with empty file arrays...\n";
    
    $rentalCodes = RentalCode::where(function($query) {
        $query->where('client_contract', 'like', '%{}%')
              ->orWhere('payment_proof', 'like', '%{}%')
              ->orWhere('client_id_document', 'like', '%{}%')
              ->orWhere('contact_images', 'like', '%{}%');
    })->get();
    
    echo "Found {$rentalCodes->count()} rental codes with empty file arrays\n\n";
    
    $fixedCount = 0;
    $skippedCount = 0;
    
    foreach ($rentalCodes as $rentalCode) {
        echo "📋 Processing rental code: {$rentalCode->rental_code}\n";
        
        $updated = false;
        
        // Fix client_contract field
        if ($rentalCode->client_contract && strpos($rentalCode->client_contract, '{}') !== false) {
            echo "   🔧 Fixing client_contract field\n";
            $rentalCode->update(['client_contract' => null]);
            $updated = true;
        }
        
        // Fix payment_proof field
        if ($rentalCode->payment_proof && strpos($rentalCode->payment_proof, '{}') !== false) {
            echo "   🔧 Fixing payment_proof field\n";
            $rentalCode->update(['payment_proof' => null]);
            $updated = true;
        }
        
        // Fix client_id_document field
        if ($rentalCode->client_id_document && strpos($rentalCode->client_id_document, '{}') !== false) {
            echo "   🔧 Fixing client_id_document field\n";
            $rentalCode->update(['client_id_document' => null]);
            $updated = true;
        }
        
        // Fix contact_images field
        if ($rentalCode->contact_images && strpos($rentalCode->contact_images, '{}') !== false) {
            echo "   🔧 Fixing contact_images field\n";
            $rentalCode->update(['contact_images' => null]);
            $updated = true;
        }
        
        if ($updated) {
            $fixedCount++;
            echo "   ✅ Fixed rental code {$rentalCode->rental_code}\n";
        } else {
            $skippedCount++;
            echo "   ⏭️  Skipped rental code {$rentalCode->rental_code} (no empty arrays found)\n";
        }
        
        echo "\n";
    }
    
    echo "2. Checking for rental codes with valid file paths...\n";
    
    $validRentalCodes = RentalCode::where(function($query) {
        $query->whereNotNull('client_contract')
              ->orWhereNotNull('payment_proof')
              ->orWhereNotNull('client_id_document')
              ->orWhereNotNull('contact_images');
    })->get();
    
    echo "Found {$validRentalCodes->count()} rental codes with file data\n\n";
    
    foreach ($validRentalCodes as $rentalCode) {
        echo "📋 Rental code: {$rentalCode->rental_code}\n";
        
        // Check client_contract
        if ($rentalCode->client_contract) {
            $contracts = json_decode($rentalCode->client_contract, true);
            if (is_array($contracts) && count($contracts) > 0) {
                echo "   📄 Client contracts: " . count($contracts) . " files\n";
                foreach ($contracts as $contract) {
                    if (Storage::disk('public')->exists($contract)) {
                        echo "     ✅ {$contract}\n";
                    } else {
                        echo "     ❌ {$contract} (file not found)\n";
                    }
                }
            }
        }
        
        // Check payment_proof
        if ($rentalCode->payment_proof) {
            $proofs = json_decode($rentalCode->payment_proof, true);
            if (is_array($proofs) && count($proofs) > 0) {
                echo "   💰 Payment proofs: " . count($proofs) . " files\n";
                foreach ($proofs as $proof) {
                    if (Storage::disk('public')->exists($proof)) {
                        echo "     ✅ {$proof}\n";
                    } else {
                        echo "     ❌ {$proof} (file not found)\n";
                    }
                }
            }
        }
        
        // Check client_id_document
        if ($rentalCode->client_id_document) {
            $ids = json_decode($rentalCode->client_id_document, true);
            if (is_array($ids) && count($ids) > 0) {
                echo "   🆔 Client ID documents: " . count($ids) . " files\n";
                foreach ($ids as $id) {
                    if (Storage::disk('public')->exists($id)) {
                        echo "     ✅ {$id}\n";
                    } else {
                        echo "     ❌ {$id} (file not found)\n";
                    }
                }
            }
        }
        
        // Check contact_images
        if ($rentalCode->contact_images) {
            $images = json_decode($rentalCode->contact_images, true);
            if (is_array($images) && count($images) > 0) {
                echo "   📸 Contact images: " . count($images) . " files\n";
                foreach ($images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        echo "     ✅ {$image}\n";
                    } else {
                        echo "     ❌ {$image} (file not found)\n";
                    }
                }
            }
        }
        
        echo "\n";
    }
    
    echo "3. Summary of fixes:\n";
    echo "   ✅ Fixed rental codes: {$fixedCount}\n";
    echo "   ⏭️  Skipped rental codes: {$skippedCount}\n";
    echo "   📊 Total processed: " . ($fixedCount + $skippedCount) . "\n\n";
    
    echo "🎉 Database cleanup completed!\n";
    echo "\n💡 What was fixed:\n";
    echo "- Removed empty JSON arrays like [{},{},{},{}]\n";
    echo "- Set file fields to NULL when they contain empty objects\n";
    echo "- Preserved valid file paths\n";
    echo "\n🔧 Next steps:\n";
    echo "1. Re-upload files through the edit form if needed\n";
    echo "2. The file upload system should now work correctly\n";
    echo "3. New uploads will store proper file paths\n";

} catch (Exception $e) {
    echo "❌ Fix failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
