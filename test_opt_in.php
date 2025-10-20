<?php

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio credentials
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$whatsappNumber = $_ENV['TWILIO_WHATSAPP_NUMBER'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

echo "=== WhatsApp Opt-In Test ===\n";
echo "This script will help you test the opt-in process.\n\n";

echo "📱 STEP 1: Send a message from your phone\n";
echo "From your phone (+447947768707), send a WhatsApp message to: +15558664742\n";
echo "Message content: 'Hello' or 'Test' or anything\n";
echo "Press Enter after you've sent the message...\n";
readline();

echo "\n📤 STEP 2: Testing response message...\n";

try {
    $client = new Client($sid, $token);
    
    // Send a response message (this should work after opt-in)
    $message = $client->messages->create(
        $testNumber, // to
        [
            "from" => $whatsappNumber,
            "body" => "✅ Great! You've successfully opted in. This message should be delivered now!"
        ]
    );
    
    echo "✅ Response message sent!\n";
    echo "Message SID: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    
    echo "\n📱 Check your WhatsApp - you should receive the message now!\n";
    
    // Wait a moment and check status
    sleep(3);
    
    $updatedMessage = $client->messages($message->sid)->fetch();
    echo "\nUpdated Status: " . $updatedMessage->status . "\n";
    
    if ($updatedMessage->status === 'delivered') {
        echo "🎉 SUCCESS! Message was delivered!\n";
    } elseif ($updatedMessage->status === 'sent') {
        echo "📤 Message sent to WhatsApp, should be delivered soon\n";
    } else {
        echo "⚠️ Status: " . $updatedMessage->status . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Next Steps for Production ===\n";
echo "1. Set up approved message templates in Twilio Console\n";
echo "2. Use templates for first contact with new customers\n";
echo "3. After customer responds, you have 24 hours for free-form messages\n";
echo "4. Consider using WhatsApp Business Manager for template approval\n";

