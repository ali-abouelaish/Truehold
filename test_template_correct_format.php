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

echo "=== WhatsApp Template Format Test ===\n";
echo "Testing different template formats...\n\n";

try {
    $client = new Client($sid, $token);
    
    echo "📋 Template Usage Options:\n";
    echo "1. Using template name in body\n";
    echo "2. Using template with parameters\n";
    echo "3. Using messaging service with template\n\n";
    
    // Method 1: Try using template name in body
    echo "🔄 Method 1: Template name in body...\n";
    try {
        $message1 = $client->messages->create(
            $testNumber,
            [
                "from" => $whatsappNumber,
                "body" => "{{1}}", // This is how you reference template parameters
                "messagingServiceSid" => "MG340cc66c893c882257ad8f3085823822"
            ]
        );
        echo "✅ Method 1 sent: " . $message1->sid . "\n";
    } catch (Exception $e) {
        echo "❌ Method 1 failed: " . $e->getMessage() . "\n";
    }
    
    // Method 2: Try with template name
    echo "\n🔄 Method 2: Using template name...\n";
    try {
        $message2 = $client->messages->create(
            $testNumber,
            [
                "from" => $whatsappNumber,
                "body" => "Hello! This is a test message.",
                "messagingServiceSid" => "MG340cc66c893c882257ad8f3085823822"
            ]
        );
        echo "✅ Method 2 sent: " . $message2->sid . "\n";
    } catch (Exception $e) {
        echo "❌ Method 2 failed: " . $e->getMessage() . "\n";
    }
    
    // Method 3: Try with specific template format
    echo "\n🔄 Method 3: Template with content variables...\n";
    try {
        $message3 = $client->messages->create(
            $testNumber,
            [
                "from" => $whatsappNumber,
                "body" => "Hello! This is a test message from your CRM system.",
                "messagingServiceSid" => "MG340cc66c893c882257ad8f3085823822"
            ]
        );
        echo "✅ Method 3 sent: " . $message3->sid . "\n";
        
        // Check status after a moment
        sleep(3);
        $updatedMessage = $client->messages($message3->sid)->fetch();
        echo "Status: " . $updatedMessage->status . "\n";
        
        if ($updatedMessage->status === 'delivered') {
            echo "🎉 SUCCESS! Template message delivered!\n";
        } elseif ($updatedMessage->status === 'undelivered') {
            echo "❌ Still undelivered. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Method 3 failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "\n";
}

echo "\n=== Template Setup Guide ===\n";
echo "To use templates correctly:\n";
echo "1. Go to Twilio Console > Messaging > Services\n";
echo "2. Select your service (MG340cc66c893c882257ad8f3085823822)\n";
echo "3. Go to 'Templates' section\n";
echo "4. Create a template with this format:\n";
echo "   - Name: 'hello_world'\n";
echo "   - Category: 'UTILITY'\n";
echo "   - Language: 'en'\n";
echo "   - Content: 'Hello {{1}}, this is a test message from {{2}}.'\n";
echo "5. Submit for approval (takes 24-48 hours)\n";
echo "6. Once approved, use like this:\n";
echo "   body: 'Hello John, this is a test message from CRM.'\n";
echo "   messagingServiceSid: 'MG340cc66c893c882257ad8f3085823822'\n";

echo "\n=== Alternative: Use Opt-In Method ===\n";
echo "For immediate testing:\n";
echo "1. Send a message from +447947768707 to +15558664742\n";
echo "2. Then send messages normally (no template needed)\n";
echo "3. This works for 24 hours after their message\n";

