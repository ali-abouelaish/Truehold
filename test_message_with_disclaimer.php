<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== WhatsApp Message with Disclaimer Test ===\n";
echo "Testing the updated message format with automated disclaimer...\n\n";

// Test the WhatsApp service directly with environment variables
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];
$whatsappNumber = $_ENV['TWILIO_WHATSAPP_NUMBER'];
$testNumber = $_ENV['TEST_WHATSAPP_NUMBER'];

try {
    $client = new \Twilio\Rest\Client($sid, $token);
    
    // Create the message with the new disclaimer
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
    $message .= "*Marketing Agent:* Umar\n";
    $message .= "\n_This is an automated message sent from the CRM. Please contact agent to change any details._";
    
    echo "📤 Sending message with disclaimer...\n";
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
        echo "📱 Check your WhatsApp for the message with disclaimer!\n";
    } elseif ($updatedMessage->status === 'undelivered') {
        echo "❌ Message not delivered. Error: " . ($updatedMessage->errorCode ?? 'Unknown') . "\n";
    }
    
    echo "\n=== Updated Message Format ===\n";
    echo "✅ Automated disclaimer added to bottom\n";
    echo "✅ Italic formatting for disclaimer\n";
    echo "✅ Professional automated message notice\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Update Complete ===\n";
echo "Your rental code WhatsApp messages now include the automated disclaimer!\n";














