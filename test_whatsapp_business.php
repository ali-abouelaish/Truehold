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

echo "=== WhatsApp Business API Test ===\n";
echo "From: " . $whatsappNumber . "\n";
echo "To: " . $testNumber . "\n";
echo "Account SID: " . $sid . "\n\n";

try {
    $client = new Client($sid, $token);
    
    // Test message
    $message = $client->messages->create(
        $testNumber, // to
        [
            "from" => $whatsappNumber,
            "body" => "✅ Hello! This is a test message from your Laravel CRM WhatsApp Business integration. Your setup is working correctly!"
        ]
    );
    
    echo "✅ Message sent successfully!\n";
    echo "Message SID: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    echo "To: " . $message->to . "\n";
    echo "From: " . $message->from . "\n";
    echo "Body: " . $message->body . "\n";
    
    echo "\n=== Next Steps ===\n";
    echo "1. Check your WhatsApp for the message\n";
    echo "2. If not received, check Twilio Console for delivery status\n";
    echo "3. Verify the recipient number is correct and has WhatsApp\n";
    echo "4. For first messages, you may need to use approved message templates\n";
    
} catch (Exception $e) {
    echo "❌ Error sending message: " . $e->getMessage() . "\n";
    
    // Check if it's a template message issue
    if (strpos($e->getMessage(), 'template') !== false) {
        echo "\n💡 This might be a template message issue.\n";
        echo "For WhatsApp Business API, first messages often need to use approved templates.\n";
        echo "Check your Twilio Console for approved message templates.\n";
    }
}
