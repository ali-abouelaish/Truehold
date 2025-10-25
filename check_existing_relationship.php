<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Checking Existing WhatsApp Relationship ===\n";
echo "Since CRM has sent messages before, investigating why it's failing now...\n\n";

// Test the WhatsApp service directly with environment variables
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

echo "🔍 POSSIBLE CAUSES:\n";
echo "1. WhatsApp Business account status changed\n";
echo "2. Message template requirements changed\n";
echo "3. Rate limiting or account restrictions\n";
echo "4. WhatsApp Business API policy changes\n";
echo "5. Twilio configuration changes\n\n";

try {
    $client = new \Twilio\Rest\Client($sid, $token);
    
    echo "📤 Testing with existing relationship...\n";
    
    // Try a very simple message first
    $simpleMessage = "Test message to existing contact";
    
    $result = $client->messages->create(
        $testNumber,
        [
            "from" => "whatsapp:+15558664742",
            "body" => $simpleMessage
        ]
    );
    
    echo "✅ Message sent: " . $result->sid . "\n";
    echo "Status: " . $result->status . "\n";
    
    // Wait and check status
    sleep(5);
    $updatedMessage = $client->messages($result->sid)->fetch();
    echo "Updated Status: " . $updatedMessage->status . "\n";
    
    if ($updatedMessage->status === 'delivered') {
        echo "🎉 SUCCESS! Existing relationship still works!\n";
        echo "The issue might be with the message content or format.\n";
    } elseif ($updatedMessage->status === 'failed') {
        echo "❌ Failed. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
        echo "This suggests the relationship may have expired or been reset.\n";
    } elseif ($updatedMessage->status === 'undelivered') {
        echo "❌ Undelivered. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
        echo "This suggests WhatsApp Business account issues.\n";
    }
    
    // Check recent messages to see if there's a pattern
    echo "\n🔍 Checking recent message history...\n";
    try {
        $messages = $client->messages->read([
            'to' => $testNumber,
            'limit' => 5
        ]);
        
        echo "Recent messages to this number:\n";
        foreach ($messages as $msg) {
            echo "- " . $msg->dateCreated->format('Y-m-d H:i:s') . " | " . $msg->status . " | " . $msg->sid . "\n";
        }
    } catch (Exception $e) {
        echo "Could not fetch message history: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SOLUTIONS FOR EXISTING RELATIONSHIPS ===\n";
echo "1. **Check WhatsApp Business Account Status:**\n";
echo "   - Go to Meta Business Manager\n";
echo "   - Check if account is still active\n";
echo "   - Look for any suspension notices\n\n";

echo "2. **Verify Twilio Configuration:**\n";
echo "   - Check Twilio Console for any changes\n";
echo "   - Verify WhatsApp Business number is still active\n";
echo "   - Check for any account restrictions\n\n";

echo "3. **Message Template Requirements:**\n";
echo "   - WhatsApp may now require templates for certain messages\n";
echo "   - Check if your message format needs approval\n";
echo "   - Consider using approved message templates\n\n";

echo "4. **Rate Limiting:**\n";
echo "   - You may have hit WhatsApp rate limits\n";
echo "   - Wait a few hours and try again\n";
echo "   - Check your message volume\n\n";

echo "5. **Re-establish Relationship:**\n";
echo "   - Even with existing relationships, you might need to re-opt-in\n";
echo "   - Send a new message from +447947768707 to +15558664742\n";
echo "   - This refreshes the relationship\n\n";

echo "📞 **IMMEDIATE ACTION:**\n";
echo "1. Send a fresh message from +447947768707 to +15558664742\n";
echo "2. Wait for confirmation\n";
echo "3. Then try creating a rental code\n";
echo "4. If still failing, contact Twilio support with your account details\n";











