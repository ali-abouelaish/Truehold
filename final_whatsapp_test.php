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

echo "=== Final WhatsApp Test ===\n";
echo "Testing all possible methods...\n\n";

// Method 1: Direct WhatsApp (should work after opt-in)
echo "🔄 Method 1: Direct WhatsApp (after opt-in)...\n";
echo "First, send a message from +447947768707 to +15558664742\n";
echo "Then press Enter to continue...\n";
readline();

try {
    $client = new Client($sid, $token);
    
    $message = $client->messages->create(
        $testNumber,
        [
            "from" => $whatsappNumber,
            "body" => "✅ SUCCESS! This message was sent after opt-in. Your WhatsApp integration is working!"
        ]
    );
    
    echo "✅ Message sent: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    
    // Check status
    sleep(3);
    $updatedMessage = $client->messages($message->sid)->fetch();
    echo "Updated Status: " . $updatedMessage->status . "\n";
    
    if ($updatedMessage->status === 'delivered') {
        echo "🎉 SUCCESS! Direct WhatsApp messaging works after opt-in!\n";
    } else {
        echo "❌ Still having issues. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Summary & Next Steps ===\n";
echo "✅ WORKING SOLUTION:\n";
echo "1. Recipient sends message to +15558664742 first\n";
echo "2. You can then send messages for 24 hours\n";
echo "3. Use this for immediate testing\n\n";

echo "📋 FOR PRODUCTION:\n";
echo "1. Set up approved message templates\n";
echo "2. Templates take 24-48 hours to approve\n";
echo "3. Use templates for first contact\n";
echo "4. After customer responds, use free-form messages\n\n";

echo "🔧 TEMPLATE SETUP:\n";
echo "1. Go to Twilio Console > Messaging > Services\n";
echo "2. Select your service: MG340cc66c893c882257ad8f3085823822\n";
echo "3. Go to Templates section\n";
echo "4. Create template with name, category, language\n";
echo "5. Submit for approval\n";
echo "6. Once approved, use in your application\n\n";

echo "📱 IMMEDIATE TEST:\n";
echo "Send a WhatsApp message from +447947768707 to +15558664742\n";
echo "Then run this script again - it should work!\n";














