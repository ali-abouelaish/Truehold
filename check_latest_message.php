<?php

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio credentials
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];

// Latest message SID from the test
$messageSid = 'SM1777f136ae139cdb15c8a17b13496737';

try {
    $client = new Client($sid, $token);
    
    // Fetch the message details
    $message = $client->messages($messageSid)->fetch();
    
    echo "=== Latest WhatsApp Message Status ===\n";
    echo "Message SID: " . $message->sid . "\n";
    echo "Status: " . $message->status . "\n";
    echo "To: " . $message->to . "\n";
    echo "From: " . $message->from . "\n";
    echo "Body: " . $message->body . "\n";
    echo "Date Created: " . $message->dateCreated->format('Y-m-d H:i:s') . "\n";
    echo "Date Updated: " . $message->dateUpdated->format('Y-m-d H:i:s') . "\n";
    echo "Error Code: " . ($message->errorCode ?? 'None') . "\n";
    echo "Error Message: " . ($message->errorMessage ?? 'None') . "\n";
    echo "Price: " . ($message->price ?? 'N/A') . "\n";
    echo "Price Unit: " . ($message->priceUnit ?? 'N/A') . "\n";
    
    // Status interpretation
    echo "\n=== Status Interpretation ===\n";
    switch ($message->status) {
        case 'queued':
            echo "Message is queued for delivery\n";
            break;
        case 'sending':
            echo "Message is being sent\n";
            break;
        case 'sent':
            echo "Message was sent to WhatsApp but may not be delivered yet\n";
            break;
        case 'delivered':
            echo "Message was delivered to recipient's device\n";
            break;
        case 'read':
            echo "Message was read by recipient\n";
            break;
        case 'failed':
            echo "Message failed to send\n";
            echo "Error: " . ($message->errorMessage ?? 'Unknown error') . "\n";
            break;
        case 'undelivered':
            echo "Message was sent but not delivered\n";
            echo "Error Code: " . ($message->errorCode ?? 'None') . "\n";
            break;
        default:
            echo "Unknown status: " . $message->status . "\n";
    }
    
    // Check for specific error codes
    if ($message->errorCode) {
        echo "\n=== Error Code Analysis ===\n";
        switch ($message->errorCode) {
            case '63016':
                echo "Error 63016: Message undelivered - likely due to:\n";
                echo "- Recipient hasn't opted in to receive messages\n";
                echo "- Message sent outside 24-hour window without template\n";
                echo "- Recipient has blocked your number\n";
                echo "- Invalid or inactive WhatsApp number\n";
                break;
            case '63007':
                echo "Error 63007: Message undelivered - recipient not on WhatsApp\n";
                break;
            case '63017':
                echo "Error 63017: Message undelivered - recipient has opted out\n";
                break;
            default:
                echo "Error " . $message->errorCode . ": Check Twilio documentation\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== WhatsApp Business API Requirements ===\n";
echo "1. For first messages to new contacts, you need approved message templates\n";
echo "2. After recipient responds, you can send free-form messages for 24 hours\n";
echo "3. Recipients must opt-in to receive messages from your business\n";
echo "4. Check your Twilio Console for approved message templates\n";
