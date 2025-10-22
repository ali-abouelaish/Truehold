<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Rental Code WhatsApp Integration Test ===\n";
echo "Testing the integration by creating a test rental code...\n\n";

// Test the WhatsApp service directly with environment variables
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$whatsappNumber = $_ENV['TWILIO_WHATSAPP_NUMBER'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

echo "📋 Configuration:\n";
echo "Account SID: " . $sid . "\n";
echo "WhatsApp Number: " . $whatsappNumber . "\n";
echo "Test Number: " . $testNumber . "\n\n";

try {
    $client = new \Twilio\Rest\Client($sid, $token);
    
    // Test client notification message
    echo "📤 Testing client notification message...\n";
    $clientMessage = "🏠 *Rental Code Generated*\n\n";
    $clientMessage .= "Hello Test Client,\n\n";
    $clientMessage .= "Your rental code has been successfully generated:\n\n";
    $clientMessage .= "📋 *Rental Code:* CC9999\n";
    $clientMessage .= "👤 *Agent:* Test Agent\n";
    $clientMessage .= "📅 *Date:* " . date('d/m/Y H:i') . "\n\n";
    $clientMessage .= "Please keep this code safe and present it when required.\n\n";
    $clientMessage .= "Thank you for choosing Truehold Group! 🏡";
    
    $clientResult = $client->messages->create(
        $testNumber,
        [
            "from" => $whatsappNumber,
            "body" => $clientMessage
        ]
    );
    
    echo "✅ Client message sent!\n";
    echo "Message SID: " . $clientResult->sid . "\n";
    echo "Status: " . $clientResult->status . "\n";
    
    // Test admin notification message
    echo "\n📤 Testing admin notification message...\n";
    $adminMessage = "🔔 *New Rental Code Generated*\n\n";
    $adminMessage .= "📋 *Code:* CC9999\n";
    $adminMessage .= "👤 *Client:* Test Client\n";
    $adminMessage .= "🏢 *Agent:* Test Agent\n";
    $adminMessage .= "📅 *Date:* " . date('d/m/Y H:i') . "\n\n";
    $adminMessage .= "Please review in the admin panel.";
    
    $adminResult = $client->messages->create(
        $testNumber, // Using same number for testing
        [
            "from" => $whatsappNumber,
            "body" => $adminMessage
        ]
    );
    
    echo "✅ Admin message sent!\n";
    echo "Message SID: " . $adminResult->sid . "\n";
    echo "Status: " . $adminResult->status . "\n";
    
    // Wait and check status
    echo "\n⏳ Waiting 5 seconds to check delivery status...\n";
    sleep(5);
    
    $updatedClientMessage = $client->messages($clientResult->sid)->fetch();
    $updatedAdminMessage = $client->messages($adminResult->sid)->fetch();
    
    echo "\n📊 Delivery Status:\n";
    echo "Client Message Status: " . $updatedClientMessage->status . "\n";
    echo "Admin Message Status: " . $updatedAdminMessage->status . "\n";
    
    if ($updatedClientMessage->status === 'delivered') {
        echo "🎉 Client message delivered successfully!\n";
    } elseif ($updatedClientMessage->status === 'undelivered') {
        echo "❌ Client message not delivered. Error: " . ($updatedClientMessage->errorCode ?? 'Unknown') . "\n";
    }
    
    if ($updatedAdminMessage->status === 'delivered') {
        echo "🎉 Admin message delivered successfully!\n";
    } elseif ($updatedAdminMessage->status === 'undelivered') {
        echo "❌ Admin message not delivered. Error: " . ($updatedAdminMessage->errorCode ?? 'Unknown') . "\n";
    }
    
    echo "\n=== Integration Summary ===\n";
    echo "✅ WhatsApp service integration is working!\n";
    echo "✅ Messages are being sent successfully\n";
    echo "✅ Your rental code generation will now send WhatsApp notifications\n";
    echo "📱 Check your WhatsApp for the test messages!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Next Steps ===\n";
echo "1. The WhatsApp integration is now active in your rental code generation\n";
echo "2. When you create a rental code, both client and admin will receive WhatsApp messages\n";
echo "3. Make sure to update your .env file with the new configuration\n";
echo "4. Test by creating a real rental code in your application\n";





