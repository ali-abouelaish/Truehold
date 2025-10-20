<?php

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio credentials
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];

// New message SID from the test
$messageSid = 'SM1b42609894ce0e5ecefdb19683fef3b3';

try {
    $client = new Client($sid, $token);
    
    // Fetch the message details
    $message = $client->messages($messageSid)->fetch();
    
    echo "=== New WhatsApp Message Status ===\n";
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
            echo "Message was not delivered\n";
            echo "This could be due to:\n";
            echo "- Recipient hasn't opted in to receive messages\n";
            echo "- Message sent outside 24-hour window without template\n";
            echo "- Recipient has blocked the number\n";
            echo "- Invalid phone number or no WhatsApp account\n";
            break;
        default:
            echo "Unknown status: " . $message->status . "\n";
    }
    
    // Check for specific error codes
    if ($message->errorCode) {
        echo "\n=== Error Code Analysis ===\n";
        switch ($message->errorCode) {
            case '63016':
                echo "Error 63016: Message undelivered\n";
                echo "Common causes:\n";
                echo "- Recipient hasn't opted in to receive messages\n";
                echo "- Message sent outside 24-hour window without approved template\n";
                echo "- Recipient has blocked your number\n";
                break;
            case '63007':
                echo "Error 63007: Invalid phone number\n";
                break;
            case '63017':
                echo "Error 63017: Message template required\n";
                break;
            default:
                echo "Error " . $message->errorCode . ": " . ($message->errorMessage ?? 'Unknown error') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Solutions ===\n";
echo "1. **Opt-in Required**: The recipient must first send a message to your WhatsApp Business number\n";
echo "2. **Use Templates**: For first messages, use approved message templates\n";
echo "3. **24-hour Window**: After recipient responds, you have 24 hours to send free-form messages\n";
echo "4. **Check Number**: Verify the phone number is correct and has WhatsApp\n";

