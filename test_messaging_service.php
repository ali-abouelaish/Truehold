<?php

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio credentials
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

// Your messaging service SID
$messagingServiceSid = 'MG340cc66c893c882257ad8f3085823822';

echo "=== Messaging Service Test ===\n";
echo "Using messaging service for WhatsApp...\n\n";

try {
    $client = new Client($sid, $token);
    
    echo "📤 Sending message via messaging service...\n";
    
    // Use messaging service instead of direct WhatsApp number
    $message = $client->messages->create(
        $testNumber, // to
        [
            "messagingServiceSid" => $messagingServiceSid,
            "body" => "Hello! This is a test message sent via your messaging service. This should work better than direct WhatsApp messaging."
        ]
    );
    
    echo "✅ Message sent successfully!\n";
    echo "Message SID: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    echo "To: " . $message->to . "\n";
    echo "Messaging Service: " . $messagingServiceSid . "\n";
    
    // Wait and check status
    echo "\n⏳ Waiting 5 seconds to check delivery...\n";
    sleep(5);
    
    $updatedMessage = $client->messages($message->sid)->fetch();
    echo "Updated Status: " . $updatedMessage->status . "\n";
    
    if ($updatedMessage->status === 'delivered') {
        echo "🎉 SUCCESS! Message delivered via messaging service!\n";
        echo "📱 Check your WhatsApp - you should have received the message!\n";
    } elseif ($updatedMessage->status === 'sent') {
        echo "📤 Message sent, should be delivered soon\n";
    } elseif ($updatedMessage->status === 'undelivered') {
        echo "❌ Still undelivered. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
        echo "This means the messaging service still needs template approval.\n";
    } else {
        echo "📊 Status: " . $updatedMessage->status . "\n";
    }
    
    // Show error details
    if ($updatedMessage->errorCode) {
        echo "\n🔍 Error Details:\n";
        echo "Error Code: " . $updatedMessage->errorCode . "\n";
        echo "Error Message: " . ($updatedMessage->errorMessage ?? 'None') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "If this still fails with error 63016:\n";
echo "1. Your template needs approval (24-48 hours)\n";
echo "2. Use the opt-in method for immediate testing\n";
echo "3. Send a message from +447947768707 to +15558664742 first\n";
echo "4. Then try sending messages normally\n";














