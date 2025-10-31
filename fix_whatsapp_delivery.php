<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== WhatsApp Delivery Fix ===\n";
echo "Addressing the delivery issues with WhatsApp messages...\n\n";

// Test the WhatsApp service directly with environment variables
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$whatsappNumber = $_ENV['TWILIO_WHATSAPP_NUMBER'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

echo "📋 Current Configuration:\n";
echo "Account SID: " . $sid . "\n";
echo "WhatsApp Number: " . $whatsappNumber . "\n";
echo "Test Number: " . $testNumber . "\n\n";

echo "🔍 Error 63112 Analysis:\n";
echo "This error typically means:\n";
echo "1. WhatsApp Business API restrictions\n";
echo "2. Message template requirements not met\n";
echo "3. Recipient opt-in issues\n";
echo "4. Rate limiting or account restrictions\n\n";

echo "🛠️ SOLUTIONS TO TRY:\n\n";

echo "1. **OPT-IN METHOD (Recommended):**\n";
echo "   - Send a WhatsApp message from +447947768707 to +15558664742\n";
echo "   - Send any message like 'Hello' or 'Test'\n";
echo "   - Wait for confirmation\n";
echo "   - Then try sending messages again\n\n";

echo "2. **TEST WITH SIMPLER MESSAGE:**\n";
try {
    $client = new \Twilio\Rest\Client($sid, $token);
    
    // Try a very simple message first
    $simpleMessage = "Hello! This is a test message from your CRM system.";
    
    echo "📤 Testing with simple message...\n";
    $result = $client->messages->create(
        $testNumber,
        [
            "from" => $whatsappNumber,
            "body" => $simpleMessage
        ]
    );
    
    echo "✅ Simple message sent!\n";
    echo "Message SID: " . $result->sid . "\n";
    echo "Status: " . $result->status . "\n";
    
    // Wait and check status
    sleep(3);
    $updatedMessage = $client->messages($result->sid)->fetch();
    echo "Updated Status: " . $updatedMessage->status . "\n";
    
    if ($updatedMessage->status === 'delivered') {
        echo "🎉 Simple message delivered! The issue might be with the message length or format.\n";
    } elseif ($updatedMessage->status === 'failed') {
        echo "❌ Simple message also failed. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
        echo "This suggests a fundamental opt-in or configuration issue.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error sending simple message: " . $e->getMessage() . "\n";
}

echo "\n3. **CHECK TWILIO CONSOLE:**\n";
echo "   - Go to Twilio Console > Monitor > Logs > Messaging\n";
echo "   - Look for your messages and check detailed error information\n";
echo "   - Check if your WhatsApp Business number is properly configured\n\n";

echo "4. **VERIFY WHATSAPP BUSINESS SETUP:**\n";
echo "   - Ensure your WhatsApp Business number (+15558664742) is active\n";
echo "   - Check if you need to use message templates for first contact\n";
echo "   - Verify your Twilio account has proper WhatsApp permissions\n\n";

echo "5. **ALTERNATIVE: USE MESSAGING SERVICE:**\n";
echo "   - Try using the messaging service instead of direct WhatsApp number\n";
echo "   - This might bypass some restrictions\n\n";

echo "=== IMMEDIATE ACTION REQUIRED ===\n";
echo "1. Send a WhatsApp message from +447947768707 to +15558664742\n";
echo "2. This will establish the opt-in relationship\n";
echo "3. Then try creating a rental code in your application\n";
echo "4. The WhatsApp messages should work after opt-in\n\n";

echo "📱 The opt-in method is the most reliable solution for WhatsApp Business API!\n";














