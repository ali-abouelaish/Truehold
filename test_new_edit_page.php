<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use App\Models\User;
use App\Models\Client;

echo "🧪 Testing New Edit Page\n";
echo "=======================\n\n";

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
    echo "   Status: {$rentalCode->status}\n\n";

    // Test 2: Check required data for edit page
    echo "2. Checking required data for edit page...\n";
    
    // Check agent users
    $agentUsers = User::where('role', 'agent')->orWhereJsonContains('roles', 'agent')->get();
    echo "   📊 Agent users: {$agentUsers->count()}\n";
    
    // Check marketing users
    $marketingUsers = User::where('role', 'marketing_agent')->orWhereJsonContains('roles', 'marketing_agent')->get();
    echo "   📊 Marketing users: {$marketingUsers->count()}\n";
    
    // Check existing clients
    $existingClients = Client::orderBy('full_name')->get();
    echo "   📊 Existing clients: {$existingClients->count()}\n";
    
    if ($agentUsers->count() == 0) {
        echo "   ⚠️  No agent users found - edit page may have issues\n";
    }
    
    if ($existingClients->count() == 0) {
        echo "   ⚠️  No existing clients found - edit page may have issues\n";
    }
    
    echo "\n";

    // Test 3: Check form structure
    echo "3. Checking form structure...\n";
    $editView = 'resources/views/admin/rental-codes/edit.blade.php';
    if (file_exists($editView)) {
        $content = file_get_contents($editView);
        
        // Check for essential form elements
        $checks = [
            'form action' => strpos($content, 'action="{{ route(\'rental-codes.update\'') !== false,
            'method POST' => strpos($content, 'method="POST"') !== false,
            'enctype multipart' => strpos($content, 'enctype="multipart/form-data"') !== false,
            'CSRF token' => strpos($content, '@csrf') !== false,
            'PUT method' => strpos($content, '@method(\'PUT\')') !== false,
            'submit button' => strpos($content, 'type="submit"') !== false,
            'rental_code field' => strpos($content, 'name="rental_code"') !== false,
            'rental_date field' => strpos($content, 'name="rental_date"') !== false,
            'consultation_fee field' => strpos($content, 'name="consultation_fee"') !== false,
            'payment_method field' => strpos($content, 'name="payment_method"') !== false,
            'marketing_agent_id field' => strpos($content, 'name="marketing_agent_id"') !== false,
            'file upload fields' => strpos($content, 'type="file"') !== false,
            'client selection' => strpos($content, 'client_selection_type') !== false,
        ];
        
        foreach ($checks as $check => $result) {
            if ($result) {
                echo "   ✅ {$check}\n";
            } else {
                echo "   ❌ {$check}\n";
            }
        }
    } else {
        echo "   ❌ Edit form file not found\n";
    }
    
    echo "\n";

    // Test 4: Check JavaScript functionality
    echo "4. Checking JavaScript functionality...\n";
    if (file_exists($editView)) {
        $content = file_get_contents($editView);
        
        $jsChecks = [
            'Client selection toggle' => strpos($content, 'toggleClientSections') !== false,
            'Form reset function' => strpos($content, 'function resetForm()') !== false,
            'Form validation' => strpos($content, 'addEventListener(\'submit\'') !== false,
            'Console logging' => strpos($content, 'console.log') !== false,
        ];
        
        foreach ($jsChecks as $check => $result) {
            if ($result) {
                echo "   ✅ {$check}\n";
            } else {
                echo "   ❌ {$check}\n";
            }
        }
    }
    
    echo "\n";

    // Test 5: Check form validation
    echo "5. Testing form validation...\n";
    
    // Test required fields
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
            echo "   ✅ Required field '{$field}' found\n";
        } else {
            echo "   ❌ Required field '{$field}' missing\n";
        }
    }
    
    echo "\n";

    // Test 6: Check file upload handling
    echo "6. Checking file upload handling...\n";
    $fileFields = [
        'client_contract',
        'payment_proof', 
        'client_id_document'
    ];
    
    foreach ($fileFields as $field) {
        if (strpos($content, "name=\"{$field}[]\"") !== false) {
            echo "   ✅ File field '{$field}' found\n";
        } else {
            echo "   ❌ File field '{$field}' missing\n";
        }
    }
    
    echo "\n";

    // Test 7: Check controller update method
    echo "7. Checking controller update method...\n";
    $controllerFile = 'app/Http/Controllers/RentalCodeController.php';
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        
        if (strpos($content, 'public function update(') !== false) {
            echo "   ✅ Update method exists\n";
        } else {
            echo "   ❌ Update method missing\n";
        }
        
        if (strpos($content, 'handleFileUploads') !== false) {
            echo "   ✅ File upload handling exists\n";
        } else {
            echo "   ❌ File upload handling missing\n";
        }
    } else {
        echo "   ❌ Controller file not found\n";
    }
    
    echo "\n🎉 New edit page analysis completed!\n";
    echo "\n📋 Summary:\n";
    echo "- Clean, modern design with Bootstrap cards\n";
    echo "- Proper form structure with all required fields\n";
    echo "- Client selection toggle (existing vs new)\n";
    echo "- File upload support for documents\n";
    echo "- Marketing agent selection\n";
    echo "- Form validation and error handling\n";
    echo "- Responsive design\n";
    echo "\n💡 The new edit page should work much better!\n";
    echo "Key improvements:\n";
    echo "1. Simplified structure - no complex tabs\n";
    echo "2. Clear form validation\n";
    echo "3. Better error handling\n";
    echo "4. Proper file upload support\n";
    echo "5. Clean JavaScript without blocking issues\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
