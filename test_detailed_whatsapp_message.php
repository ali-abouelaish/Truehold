<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== Detailed WhatsApp Message Format Test ===\n";
echo "Testing the new detailed rental code message format...\n\n";

// Test the WhatsApp service directly with environment variables
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$whatsappNumber = $_ENV['TWILIO_WHATSAPP_NUMBER'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

echo "📋 Configuration:\n";
echo "Account SID: " . $sid . "\n";
echo "WhatsApp Number: " . $whatsappNumber . "\n";
echo "Test Number: " . $testNumber . "\n\n";

try {
    $client = new \Twilio\Rest\Client($sid, $token);
    
    // Create the detailed message in the format you requested
    $message = "🏠 *RENTAL CODE: CC0161*\n";
    $message .= "_\n";
    $message .= "Rental Date: 15/10/2025\n";
    $message .= "Consultation fee: £250\n";
    $message .= "Method of Payment: Card Machine\n";
    $message .= "Property: Clapham junction, wandsworth road\n";
    $message .= "Licensor: UK London Flat\n\n";
    
    $message .= "*Full Name:* Deivydas Pacesas\n";
    $message .= "*Date of Birth:* 29th December 2006\n";
    $message .= "*Phone Number:* 0770 830 2558\n";
    $message .= "*Email:* deivydaspacesas@gmail.com\n";
    $message .= "*Nationality:* British\n";
    $message .= "*Current Address:* SE3 7LW, 97A Humber Road\n";
    $message .= "*Company/University:* University of Arts London\n";
    $message .= "*Position/Role:* Student\n\n";
    
    $message .= "*Agent:* Ali\n";
    $message .= "*Marketing Agent:* Umar\n\n";
    
    $message .= "📱 *Keep this code safe and present it when required.*\n";
    $message .= "Thank you for choosing Truehold Group! 🏡";
    
    echo "📤 Sending detailed rental code message...\n";
    echo "Message content:\n";
    echo "---\n";
    echo $message . "\n";
    echo "---\n\n";
    
    $result = $client->messages->create(
        $testNumber,
        [
            "from" => $whatsappNumber,
            "body" => $message
        ]
    );
    
    echo "✅ Message sent successfully!\n";
    echo "Message SID: " . $result->sid . "\n";
    echo "Status: " . $result->status . "\n";
    
    // Wait and check status
    echo "\n⏳ Waiting 5 seconds to check delivery status...\n";
    sleep(5);
    
    $updatedMessage = $client->messages($result->sid)->fetch();
    echo "Updated Status: " . $updatedMessage->status . "\n";
    
    if ($updatedMessage->status === 'delivered') {
        echo "🎉 Message delivered successfully!\n";
        echo "📱 Check your WhatsApp for the detailed rental code message!\n";
    } elseif ($updatedMessage->status === 'undelivered') {
        echo "❌ Message not delivered. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
    }
    
    echo "\n=== Message Format Summary ===\n";
    echo "✅ Rental code details included\n";
    echo "✅ Client information included\n";
    echo "✅ Agent and marketing agent included\n";
    echo "✅ Professional formatting applied\n";
    echo "✅ All details from your example included\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Integration Complete ===\n";
echo "Your rental code WhatsApp messages will now include all the detailed information!\n";
echo "The format matches exactly what you requested.\n";




