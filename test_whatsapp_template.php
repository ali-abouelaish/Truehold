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

echo "=== WhatsApp Template Message Test ===\n";
echo "From: " . $whatsappNumber . "\n";
echo "To: " . $testNumber . "\n\n";

try {
    $client = new Client($sid, $token);
    
    // Try with a simple template-like message
    $message = $client->messages->create(
        $testNumber, // to
        [
            "from" => $whatsappNumber,
            "body" => "Hello! This is a test message from Truehold Group CRM system."
        ]
    );
    
    echo "✅ Template message sent successfully!\n";
    echo "Message SID: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    
} catch (Exception $e) {
    echo "❌ Error sending template message: " . $e->getMessage() . "\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Check your Twilio Console for approved message templates\n";
echo "2. Go to: Console > Messaging > Senders > WhatsApp > Message Templates\n";
echo "3. Create or use an existing approved template\n";
echo "4. For testing, you can also try sending a message TO your business number first\n";
echo "5. Then reply within 24 hours to establish a session\n";

echo "\n=== Alternative Testing Method ===\n";
echo "1. Send a WhatsApp message FROM your phone (+447947768707) TO your business number (+15558664742)\n";
echo "2. Send any message like 'Hello' or 'Test'\n";
echo "3. Then run the test again - it should work within 24 hours\n";
