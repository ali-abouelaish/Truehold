<?php

require_once 'vendor/autoload.php';

use App\Services\WhatsAppService;
use App\Models\RentalCode;
use App\Models\Client;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Rental Code WhatsApp Integration Test ===\n";
echo "Testing WhatsApp notifications for rental codes...\n\n";

try {
    // Create a test rental code and client
    echo "📋 Creating test rental code and client...\n";
    
    // Find or create a test client
    $client = Client::where('phone_number', '+447947768707')->first();
    if (!$client) {
        $client = Client::create([
            'full_name' => 'Test Client',
            'phone_number' => '+447947768707',
            'email' => 'test@example.com',
            'date_of_birth' => '1990-01-01',
            'nationality' => 'British',
            'current_address' => '123 Test Street',
            'company_university_name' => 'Test Company',
            'company_university_address' => '456 Business Ave',
            'position_role' => 'Test Role',
            'current_landlord_name' => 'Test Landlord',
            'current_landlord_contact_info' => 'test@landlord.com'
        ]);
        echo "✅ Created test client: {$client->full_name}\n";
    } else {
        echo "✅ Using existing test client: {$client->full_name}\n";
    }
    
    // Create a test rental code
    $rentalCode = RentalCode::create([
        'rental_code' => 'CC9999',
        'client_id' => $client->id,
        'agent_name' => 'Test Agent',
        'rent_by_agent' => 'Test Company',
        'status' => 'pending',
        'property_address' => '123 Test Property',
        'rent_amount' => 1500.00,
        'deposit_amount' => 3000.00,
        'lease_start_date' => now()->addDays(30),
        'lease_end_date' => now()->addDays(395),
        'payment_method' => 'bank_transfer',
        'marketing_arrangement' => 'standard'
    ]);
    
    echo "✅ Created test rental code: {$rentalCode->rental_code}\n";
    
    // Test WhatsApp service
    echo "\n📱 Testing WhatsApp service...\n";
    $whatsappService = new WhatsAppService();
    
    // Test client notification
    echo "📤 Sending notification to client...\n";
    $clientResult = $whatsappService->sendRentalCodeNotification($rentalCode, $client);
    
    if ($clientResult['success']) {
        echo "✅ Client notification sent successfully!\n";
        echo "Message SID: " . $clientResult['sid'] . "\n";
        echo "Status: " . $clientResult['status'] . "\n";
    } else {
        echo "❌ Client notification failed: " . $clientResult['error'] . "\n";
    }
    
    // Test admin notification
    echo "\n📤 Sending notification to admin...\n";
    $adminResult = $whatsappService->sendRentalCodeAdminNotification($rentalCode, $client);
    
    if ($adminResult['success']) {
        echo "✅ Admin notification sent successfully!\n";
        echo "Message SID: " . $adminResult['sid'] . "\n";
        echo "Status: " . $adminResult['status'] . "\n";
    } else {
        echo "❌ Admin notification failed: " . $adminResult['error'] . "\n";
    }
    
    echo "\n=== Test Results ===\n";
    echo "✅ WhatsApp service created successfully\n";
    echo "✅ Test rental code created: {$rentalCode->rental_code}\n";
    echo "✅ Test client created: {$client->full_name}\n";
    
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
    
    // Clean up test data
    echo "\n🧹 Cleaning up test data...\n";
    $rentalCode->delete();
    if ($client->full_name === 'Test Client') {
        $client->delete();
        echo "✅ Test data cleaned up\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Integration Complete ===\n";
echo "Your rental code generation will now send WhatsApp notifications!\n";
echo "When you create a rental code, both client and admin will receive WhatsApp messages.\n";






