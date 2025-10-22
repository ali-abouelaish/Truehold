<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use App\Models\User;
use App\Models\Client;

echo "🔍 Debugging Update Form Issues\n";
echo "==============================\n\n";

try {
    // Test 1: Check if rental codes exist
    echo "1. Checking rental codes...\n";
    $rentalCode = RentalCode::first();
    
    if (!$rentalCode) {
        echo "❌ No rental codes found. Please create a rental code first.\n";
        exit(1);
    }
    
    echo "✅ Found rental code: {$rentalCode->rental_code}\n";
    echo "   ID: {$rentalCode->id}\n";
    echo "   Status: {$rentalCode->status}\n";
    echo "   Created: {$rentalCode->created_at}\n";
    echo "   Updated: {$rentalCode->updated_at}\n\n";

    // Test 2: Check form route
    echo "2. Checking form route...\n";
    $updateRoute = route('rental-codes.update', $rentalCode);
    echo "   Update route: {$updateRoute}\n";
    
    // Test 3: Check if controller method exists
    echo "3. Checking controller method...\n";
    $controllerFile = 'app/Http/Controllers/RentalCodeController.php';
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        if (strpos($content, 'public function update(') !== false) {
            echo "   ✅ Update method exists in controller\n";
        } else {
            echo "   ❌ Update method missing in controller\n";
        }
    } else {
        echo "   ❌ Controller file not found\n";
    }
    
    // Test 4: Check form validation
    echo "4. Checking form validation...\n";
    $editView = 'resources/views/admin/rental-codes/edit.blade.php';
    if (file_exists($editView)) {
        $content = file_get_contents($editView);
        
        // Check for form structure
        if (strpos($content, 'id="editRentalCodeForm"') !== false) {
            echo "   ✅ Form ID found\n";
        } else {
            echo "   ❌ Form ID missing\n";
        }
        
        if (strpos($content, 'method="POST"') !== false) {
            echo "   ✅ Form method is POST\n";
        } else {
            echo "   ❌ Form method not POST\n";
        }
        
        if (strpos($content, '@method(\'PUT\')') !== false) {
            echo "   ✅ PUT method override found\n";
        } else {
            echo "   ❌ PUT method override missing\n";
        }
        
        if (strpos($content, 'enctype="multipart/form-data"') !== false) {
            echo "   ✅ Form enctype is correct\n";
        } else {
            echo "   ❌ Form enctype missing\n";
        }
        
        if (strpos($content, 'type="submit"') !== false) {
            echo "   ✅ Submit button found\n";
        } else {
            echo "   ❌ Submit button missing\n";
        }
    } else {
        echo "   ❌ Edit form file not found\n";
    }
    
    // Test 5: Check for JavaScript errors
    echo "5. Checking JavaScript validation...\n";
    if (file_exists($editView)) {
        $content = file_get_contents($editView);
        
        if (strpos($content, 'addEventListener(\'submit\'') !== false) {
            echo "   ✅ Form submit event listener found\n";
        } else {
            echo "   ❌ Form submit event listener missing\n";
        }
        
        if (strpos($content, 'e.preventDefault()') !== false) {
            echo "   ⚠️  Form has preventDefault - this might block submission\n";
        } else {
            echo "   ✅ No preventDefault found\n";
        }
    }
    
    // Test 6: Check required fields
    echo "6. Checking required fields...\n";
    $requiredFields = [
        'rental_code',
        'rental_date', 
        'consultation_fee',
        'payment_method',
        'client_selection_type',
        'rent_by_agent',
        'client_count'
    ];
    
    foreach ($requiredFields as $field) {
        if (strpos($content, "name=\"{$field}\"") !== false) {
            echo "   ✅ Field '{$field}' found\n";
        } else {
            echo "   ❌ Field '{$field}' missing\n";
        }
    }
    
    // Test 7: Check for validation errors
    echo "7. Checking for potential validation issues...\n";
    
    // Check if rental_code is unique
    $duplicateCodes = RentalCode::where('rental_code', $rentalCode->rental_code)
        ->where('id', '!=', $rentalCode->id)
        ->count();
    
    if ($duplicateCodes > 0) {
        echo "   ⚠️  Duplicate rental codes found: {$duplicateCodes}\n";
    } else {
        echo "   ✅ No duplicate rental codes\n";
    }
    
    // Test 8: Check database connection
    echo "8. Testing database connection...\n";
    try {
        $testQuery = RentalCode::count();
        echo "   ✅ Database connection working\n";
        echo "   📊 Total rental codes: {$testQuery}\n";
    } catch (Exception $e) {
        echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    }
    
    // Test 9: Check for missing dependencies
    echo "9. Checking for missing dependencies...\n";
    $agentUsers = User::where('role', 'agent')->orWhereJsonContains('roles', 'agent')->count();
    $marketingUsers = User::where('role', 'marketing_agent')->orWhereJsonContains('roles', 'marketing_agent')->count();
    $clients = Client::count();
    
    echo "   📊 Agent users: {$agentUsers}\n";
    echo "   📊 Marketing users: {$marketingUsers}\n";
    echo "   📊 Clients: {$clients}\n";
    
    if ($agentUsers == 0) {
        echo "   ⚠️  No agent users found - this might cause validation errors\n";
    }
    
    if ($clients == 0) {
        echo "   ⚠️  No clients found - this might cause validation errors\n";
    }
    
    echo "\n🎉 Debug analysis completed!\n";
    echo "\n💡 Common issues and solutions:\n";
    echo "1. JavaScript validation blocking submission\n";
    echo "2. Missing required fields\n";
    echo "3. Validation errors in controller\n";
    echo "4. Database connection issues\n";
    echo "5. Missing agent users or clients\n";
    echo "\n🔧 To fix:\n";
    echo "1. Check browser console for JavaScript errors\n";
    echo "2. Ensure all required fields are filled\n";
    echo "3. Check Laravel logs for validation errors\n";
    echo "4. Verify database connection\n";

} catch (Exception $e) {
    echo "❌ Debug failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
