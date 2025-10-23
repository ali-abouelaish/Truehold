<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== WhatsApp Debug Session ===\n";
echo "Comprehensive troubleshooting for WhatsApp delivery issues...\n\n";

// Test the WhatsApp service directly with environment variables
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$whatsappNumber = $_ENV['TWILIO_WHATSAPP_NUMBER'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

echo "📋 Configuration Check:\n";
echo "Account SID: " . ($sid ? '✅ Set' : '❌ Missing') . "\n";
echo "Auth Token: " . ($token ? '✅ Set' : '❌ Missing') . "\n";
echo "WhatsApp Number: " . $whatsappNumber . "\n";
echo "Test Number: " . $testNumber . "\n\n";

if (!$sid || !$token) {
    echo "❌ CRITICAL: Missing Twilio credentials!\n";
    echo "Please check your .env file and ensure TWILIO_ACCOUNT_SID and TWILIO_AUTH_TOKEN are set.\n";
    exit;
}

try {
    $client = new \Twilio\Rest\Client($sid, $token);
    
    echo "🔍 Testing Twilio Connection...\n";
    
    // Test 1: Try sending to a different number format
    echo "\n📤 Test 1: Different number format...\n";
    try {
        $result1 = $client->messages->create(
            '+447947768707', // Without whatsapp: prefix
            [
                "from" => $whatsappNumber,
                "body" => "Test message without whatsapp prefix"
            ]
        );
        echo "✅ Message sent: " . $result1->sid . "\n";
        echo "Status: " . $result1->status . "\n";
    } catch (Exception $e) {
        echo "❌ Failed: " . $e->getMessage() . "\n";
    }
    
    // Test 2: Try with messaging service
    echo "\n📤 Test 2: Using messaging service...\n";
    try {
        $result2 = $client->messages->create(
            $testNumber,
            [
                "messagingServiceSid" => "MG340cc66c893c882257ad8f3085823822",
                "body" => "Test message via messaging service"
            ]
        );
        echo "✅ Message sent: " . $result2->sid . "\n";
        echo "Status: " . $result2->status . "\n";
    } catch (Exception $e) {
        echo "❌ Failed: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Check account status
    echo "\n🔍 Test 3: Checking account status...\n";
    try {
        $account = $client->api->accounts($sid)->fetch();
        echo "✅ Account Status: " . $account->status . "\n";
        echo "Account Type: " . $account->type . "\n";
    } catch (Exception $e) {
        echo "❌ Failed to fetch account: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Check available phone numbers
    echo "\n🔍 Test 4: Checking WhatsApp numbers...\n";
    try {
        $incomingNumbers = $client->incomingPhoneNumbers->read();
        echo "Available phone numbers: " . count($incomingNumbers) . "\n";
        foreach ($incomingNumbers as $number) {
            echo "- " . $number->phoneNumber . " (Capabilities: " . json_encode($number->capabilities) . ")\n";
        }
    } catch (Exception $e) {
        echo "❌ Failed to fetch numbers: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Try a very basic message
    echo "\n📤 Test 5: Ultra-simple message...\n";
    try {
        $result5 = $client->messages->create(
            $testNumber,
            [
                "from" => $whatsappNumber,
                "body" => "Hi"
            ]
        );
        echo "✅ Message sent: " . $result5->sid . "\n";
        echo "Status: " . $result5->status . "\n";
        
        // Wait and check status
        sleep(3);
        $updatedMessage = $client->messages($result5->sid)->fetch();
        echo "Updated Status: " . $updatedMessage->status . "\n";
        
        if ($updatedMessage->errorCode) {
            echo "Error Code: " . $updatedMessage->errorCode . "\n";
            echo "Error Message: " . ($updatedMessage->errorMessage ?? 'None') . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "This suggests a fundamental configuration issue.\n";
}

echo "\n=== TROUBLESHOOTING STEPS ===\n";
echo "1. **Check .env file**: Ensure all Twilio credentials are correct\n";
echo "2. **Verify WhatsApp Business**: Check if +15558664742 is properly configured\n";
echo "3. **Opt-in Required**: Send message from +447947768707 to +15558664742\n";
echo "4. **Check Twilio Console**: Look for detailed error messages\n";
echo "5. **Account Status**: Ensure your Twilio account is active and has credits\n";
echo "6. **WhatsApp Permissions**: Verify your account has WhatsApp Business API access\n\n";

echo "📱 **IMMEDIATE ACTION**: Send a WhatsApp message from +447947768707 to +15558664742\n";
echo "This is the most common solution for WhatsApp Business API issues.\n";








