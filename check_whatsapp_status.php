<?php

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio credentials
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];

// Message SID from your response
$messageSid = 'SMe6053fa296d1d952154363ef73d72f46';

try {
    $client = new Client($sid, $token);
    
    // Fetch the message details
    $message = $client->messages($messageSid)->fetch();
    
    echo "=== WhatsApp Message Status ===\n";
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
            echo "If using sandbox, recipient needs to join first\n";
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
        default:
            echo "Unknown status: " . $message->status . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Next Steps ===\n";
echo "1. If status is 'sent' but no delivery:\n";
echo "   - Check if recipient joined the sandbox\n";
echo "   - Verify phone number is correct\n";
echo "   - Check if number has WhatsApp\n";
echo "\n2. If status is 'failed':\n";
echo "   - Check error code and message above\n";
echo "   - Verify Twilio credentials\n";
echo "   - Check account balance\n";
echo "\n3. To join sandbox:\n";
echo "   - Send 'join <sandbox-code>' to +1 415 523 8886\n";
echo "   - Get sandbox code from Twilio Console\n";
