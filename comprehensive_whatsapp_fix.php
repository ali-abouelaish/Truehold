<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Comprehensive WhatsApp Fix ===\n";
echo "Addressing all possible WhatsApp delivery issues...\n\n";

// Test the WhatsApp service directly with environment variables
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

echo "🔍 DIAGNOSIS:\n";
echo "Error 21703: Messaging service configuration issue\n";
echo "Error 63112: WhatsApp Business API restrictions\n";
echo "Both errors suggest fundamental WhatsApp Business setup issues.\n\n";

echo "🛠️ COMPREHENSIVE SOLUTIONS:\n\n";

echo "1. **IMMEDIATE FIX - OPT-IN METHOD:**\n";
echo "   📱 Send a WhatsApp message from +447947768707 to +15558664742\n";
echo "   📱 Send any message like 'Hello' or 'Test'\n";
echo "   📱 Wait for confirmation\n";
echo "   📱 This establishes the required opt-in relationship\n\n";

echo "2. **CHECK WHATSAPP BUSINESS STATUS:**\n";
echo "   🔍 Go to your Meta Business Manager\n";
echo "   🔍 Check WhatsApp Business Account status\n";
echo "   🔍 Ensure account is not suspended or restricted\n";
echo "   🔍 Verify business verification is complete\n\n";

echo "3. **VERIFY TWILIO CONFIGURATION:**\n";
echo "   🔍 Go to Twilio Console > Messaging > Services\n";
echo "   🔍 Check service MG340cc66c893c882257ad8f3085823822\n";
echo "   🔍 Ensure it's properly configured for WhatsApp\n";
echo "   🔍 Verify sender pool includes your WhatsApp number\n\n";

echo "4. **TEST WITH DIFFERENT APPROACH:**\n";
try {
    $client = new \Twilio\Rest\Client($sid, $token);
    
    echo "📤 Testing with direct WhatsApp number (after opt-in)...\n";
    
    // Try with the original WhatsApp number approach
    $result = $client->messages->create(
        $testNumber,
        [
            "from" => "whatsapp:+15558664742",
            "body" => "Test message after opt-in"
        ]
    );
    
    echo "✅ Message sent: " . $result->sid . "\n";
    echo "Status: " . $result->status . "\n";
    
    sleep(3);
    $updatedMessage = $client->messages($result->sid)->fetch();
    echo "Updated Status: " . $updatedMessage->status . "\n";
    
    if ($updatedMessage->status === 'delivered') {
        echo "🎉 SUCCESS! Direct WhatsApp number works after opt-in!\n";
    } else {
        echo "❌ Still failing. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n5. **ALTERNATIVE: USE REGULAR SMS:**\n";
echo "   📱 As a temporary solution, you could use regular SMS\n";
echo "   📱 This would work immediately without WhatsApp restrictions\n";
echo "   📱 You can switch back to WhatsApp once issues are resolved\n\n";

echo "6. **CONTACT TWILIO SUPPORT:**\n";
echo "   📞 If all else fails, contact Twilio support\n";
echo "   📞 Provide them with your account SID and error codes\n";
echo "   📞 They can check your WhatsApp Business configuration\n\n";

echo "=== RECOMMENDED IMMEDIATE ACTION ===\n";
echo "1. **SEND OPT-IN MESSAGE**: From +447947768707 to +15558664742\n";
echo "2. **WAIT FOR CONFIRMATION**: You should receive a confirmation\n";
echo "3. **TEST RENTAL CODE CREATION**: Try creating a rental code\n";
echo "4. **CHECK DELIVERY**: The WhatsApp messages should work\n\n";

echo "📱 **The opt-in method is the most reliable solution for WhatsApp Business API!**\n";
echo "This is a standard requirement, not a technical issue.\n";






