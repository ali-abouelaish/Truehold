<?php

require_once 'vendor/autoload.php';

use App\Services\WhatsAppService;
use App\Models\RentalCode;
use App\Models\Client;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing WhatsApp Rental Code Notifications\n";
echo "============================================\n\n";

try {
    // Test 1: Initialize WhatsApp Service
    echo "1. Testing WhatsApp Service Initialization...\n";
    $whatsappService = new WhatsAppService();
    echo "✅ WhatsApp Service initialized successfully\n\n";

    // Test 2: Check Configuration
    echo "2. Checking Twilio Configuration...\n";
    $accountSid = config('services.twilio.account_sid');
    $authToken = config('services.twilio.auth_token');
    $whatsappNumber = config('services.twilio.whatsapp_number');
    $adminNumber = config('services.twilio.admin_whatsapp_number');

    echo "   Account SID: " . ($accountSid ? '✅ Configured' : '❌ Missing') . "\n";
    echo "   Auth Token: " . ($authToken ? '✅ Configured' : '❌ Missing') . "\n";
    echo "   WhatsApp Number: " . ($whatsappNumber ?: '❌ Missing') . "\n";
    echo "   Admin Number: " . ($adminNumber ?: '❌ Missing') . "\n\n";

    if (!$accountSid || !$authToken || !$whatsappNumber || !$adminNumber) {
        echo "❌ Twilio configuration incomplete. Please check your .env file.\n";
        echo "Required variables:\n";
        echo "- TWILIO_ACCOUNT_SID\n";
        echo "- TWILIO_AUTH_TOKEN\n";
        echo "- TWILIO_WHATSAPP_NUMBER\n";
        echo "- ADMIN_WHATSAPP_NUMBER\n\n";
        exit(1);
    }

    // Test 3: Find a rental code to test with
    echo "3. Finding rental code for testing...\n";
    $rentalCode = RentalCode::with('client')->first();
    
    if (!$rentalCode) {
        echo "❌ No rental codes found in database. Please create a rental code first.\n";
        exit(1);
    }

    echo "✅ Found rental code: {$rentalCode->rental_code}\n";
    echo "   Client: " . ($rentalCode->client ? $rentalCode->client->full_name : 'No client') . "\n\n";

    // Test 4: Test Admin Notification (New Rental Code)
    echo "4. Testing Admin Notification (New Rental Code)...\n";
    $result = $whatsappService->sendRentalCodeAdminNotification($rentalCode, $rentalCode->client);
    
    if ($result['success']) {
        echo "✅ Admin notification sent successfully!\n";
        echo "   Message SID: {$result['sid']}\n";
        echo "   Sent to: {$result['to']}\n";
    } else {
        echo "❌ Admin notification failed: {$result['error']}\n";
    }
    echo "\n";

    // Test 5: Test Update Notification
    echo "5. Testing Update Notification...\n";
    $result = $whatsappService->sendRentalCodeUpdateNotification($rentalCode, $rentalCode->client, 'updated');
    
    if ($result['success']) {
        echo "✅ Update notification sent successfully!\n";
        echo "   Message SID: {$result['sid']}\n";
        echo "   Sent to: {$result['to']}\n";
    } else {
        echo "❌ Update notification failed: {$result['error']}\n";
    }
    echo "\n";

    // Test 6: Test with different actions
    echo "6. Testing different notification actions...\n";
    $actions = ['created', 'updated', 'modified'];
    
    foreach ($actions as $action) {
        echo "   Testing action: {$action}...\n";
        $result = $whatsappService->sendRentalCodeUpdateNotification($rentalCode, $rentalCode->client, $action);
        
        if ($result['success']) {
            echo "   ✅ {$action} notification sent successfully!\n";
        } else {
            echo "   ❌ {$action} notification failed: {$result['error']}\n";
        }
    }
    echo "\n";

    echo "🎉 WhatsApp notification testing completed!\n";
    echo "Check your admin WhatsApp number for received messages.\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
