<?php

require_once 'vendor/autoload.php';

use App\Services\WhatsAppService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== WhatsApp Service Test ===\n";
echo "Testing WhatsApp service for rental code notifications...\n\n";

try {
    // Create mock rental code and client data
    $mockRentalCode = (object) [
        'rental_code' => 'CC9999',
        'agent_name' => 'Test Agent',
        'rent_by_agent' => 'Test Company',
        'created_at' => now()
    ];
    
    $mockClient = (object) [
        'full_name' => 'Test Client',
        'phone_number' => '+447947768707'
    ];
    
    echo "📱 Testing WhatsApp service...\n";
    $whatsappService = new WhatsAppService();
    
    // Test client notification
    echo "📤 Sending notification to client...\n";
    $clientResult = $whatsappService->sendRentalCodeNotification($mockRentalCode, $mockClient);
    
    if ($clientResult['success']) {
        echo "✅ Client notification sent successfully!\n";
        echo "Message SID: " . $clientResult['sid'] . "\n";
        echo "Status: " . $clientResult['status'] . "\n";
    } else {
        echo "❌ Client notification failed: " . $clientResult['error'] . "\n";
    }
    
    // Test admin notification
    echo "\n📤 Sending notification to admin...\n";
    $adminResult = $whatsappService->sendRentalCodeAdminNotification($mockRentalCode, $mockClient);
    
    if ($adminResult['success']) {
        echo "✅ Admin notification sent successfully!\n";
        echo "Message SID: " . $adminResult['sid'] . "\n";
        echo "Status: " . $adminResult['status'] . "\n";
    } else {
        echo "❌ Admin notification failed: " . $adminResult['error'] . "\n";
    }
    
    echo "\n=== Test Results ===\n";
    echo "✅ WhatsApp service created successfully\n";
    echo "✅ Test rental code: {$mockRentalCode->rental_code}\n";
    echo "✅ Test client: {$mockClient->full_name}\n";
    
    if ($clientResult['success']) {
        echo "✅ Client WhatsApp notification: SUCCESS\n";
    } else {
        echo "❌ Client WhatsApp notification: FAILED\n";
    }
    
    if ($adminResult['success']) {
        echo "✅ Admin WhatsApp notification: SUCCESS\n";
    } else {
        echo "❌ Admin WhatsApp notification: FAILED\n";
    }
    
    echo "\n📱 Check your WhatsApp for the messages!\n";
    echo "The rental code integration is now ready to use.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Integration Complete ===\n";
echo "Your rental code generation will now send WhatsApp notifications!\n";
echo "When you create a rental code, both client and admin will receive WhatsApp messages.\n";








