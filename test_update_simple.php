<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;

echo "🧪 Simple Update Test\n";
echo "===================\n\n";

try {
    // Get a rental code to test with
    $rentalCode = RentalCode::first();
    
    if (!$rentalCode) {
        echo "❌ No rental codes found\n";
        exit(1);
    }
    
    echo "Testing update for rental code: {$rentalCode->rental_code}\n";
    
    // Test simple update without file uploads
    $originalNotes = $rentalCode->notes;
    $newNotes = $originalNotes . ' [Updated: ' . now() . ']';
    
    echo "Original notes: {$originalNotes}\n";
    echo "New notes: {$newNotes}\n";
    
    // Perform update
    $rentalCode->update(['notes' => $newNotes]);
    
    // Refresh and check
    $rentalCode->refresh();
    
    if ($rentalCode->notes === $newNotes) {
        echo "✅ Simple update works!\n";
    } else {
        echo "❌ Simple update failed!\n";
    }
    
    // Restore original notes
    $rentalCode->update(['notes' => $originalNotes]);
    echo "✅ Restored original notes\n";
    
    echo "\n🎉 Basic update functionality is working!\n";
    echo "\n💡 The issue might be:\n";
    echo "1. JavaScript validation blocking submission\n";
    echo "2. Form validation errors\n";
    echo "3. Missing required fields\n";
    echo "4. Browser console errors\n";
    
    echo "\n🔧 To debug further:\n";
    echo "1. Open browser developer tools (F12)\n";
    echo "2. Go to Console tab\n";
    echo "3. Try to submit the form\n";
    echo "4. Look for JavaScript errors\n";
    echo "5. Check Network tab for failed requests\n";

} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
