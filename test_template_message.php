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
echo "Testing with your new message template...\n\n";

try {
    $client = new Client($sid, $token);
    
    // First, let's try to get available templates
    echo "📋 Checking for available templates...\n";
    
    try {
        // Get messaging service
        $services = $client->messaging->v1->services->read();
        $serviceSid = $services[0]->sid ?? null;
        
        if ($serviceSid) {
            echo "✅ Found messaging service: " . $serviceSid . "\n";
            
            // Try to get templates (this might not work depending on setup)
            try {
                $templates = $client->messaging->v1->services($serviceSid)->templates->read();
                echo "📝 Found " . count($templates) . " templates\n";
                
                foreach ($templates as $template) {
                    echo "Template: " . $template->name . " (Status: " . $template->status . ")\n";
                }
            } catch (Exception $e) {
                echo "⚠️ Could not fetch templates: " . $e->getMessage() . "\n";
            }
        }
    } catch (Exception $e) {
        echo "⚠️ Could not fetch messaging services: " . $e->getMessage() . "\n";
    }
    
    echo "\n📤 Sending template message...\n";
    
    // Try sending a message (this should work with templates)
    $message = $client->messages->create(
        $testNumber, // to
        [
            "from" => $whatsappNumber,
            "body" => "Hello! This is a test message from your Laravel CRM using a WhatsApp template."
        ]
    );
    
    echo "✅ Message sent successfully!\n";
    echo "Message SID: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    echo "To: " . $message->to . "\n";
    echo "From: " . $message->from . "\n";
    
    // Wait a moment and check status
    echo "\n⏳ Waiting 5 seconds to check delivery status...\n";
    sleep(5);
    
    $updatedMessage = $client->messages($message->sid)->fetch();
    echo "Updated Status: " . $updatedMessage->status . "\n";
    
    if ($updatedMessage->status === 'delivered') {
        echo "🎉 SUCCESS! Message was delivered!\n";
        echo "📱 Check your WhatsApp - you should have received the message!\n";
    } elseif ($updatedMessage->status === 'sent') {
        echo "📤 Message sent to WhatsApp, should be delivered soon\n";
    } elseif ($updatedMessage->status === 'undelivered') {
        echo "❌ Message not delivered. Error code: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
        echo "This might mean:\n";
        echo "- Template not approved yet\n";
        echo "- Template name/format incorrect\n";
        echo "- Recipient number issues\n";
    } else {
        echo "📊 Status: " . $updatedMessage->status . "\n";
    }
    
    // Show error details if any
    if ($updatedMessage->errorCode) {
        echo "\n🔍 Error Details:\n";
        echo "Error Code: " . $updatedMessage->errorCode . "\n";
        echo "Error Message: " . ($updatedMessage->errorMessage ?? 'None') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error sending message: " . $e->getMessage() . "\n";
    
    // Check if it's a template-related error
    if (strpos($e->getMessage(), 'template') !== false) {
        echo "\n💡 Template Error Detected:\n";
        echo "This might mean:\n";
        echo "1. Template not approved yet (can take 24-48 hours)\n";
        echo "2. Template name is incorrect\n";
        echo "3. Template format is wrong\n";
        echo "4. Need to use specific template syntax\n";
    }
}

echo "\n=== Next Steps ===\n";
echo "1. If successful: Your template is working! 🎉\n";
echo "2. If failed: Check template approval status in Twilio Console\n";
echo "3. Template approval can take 24-48 hours\n";
echo "4. Make sure template name matches exactly\n";

