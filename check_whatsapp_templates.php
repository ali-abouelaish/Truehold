<?php

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Twilio credentials
$sid = $_ENV['TWILIO_ACCOUNT_SID'];
$token = $_ENV['TWILIO_AUTH_TOKEN'];

try {
    $client = new Client($sid, $token);
    
    echo "=== WhatsApp Business Templates ===\n";
    echo "Checking for approved message templates...\n\n";
    
    // Get WhatsApp templates (this might not work depending on account setup)
    try {
        $templates = $client->messaging->v1->services->read();
        echo "Found " . count($templates) . " messaging services\n";
        
        foreach ($templates as $service) {
            echo "Service SID: " . $service->sid . "\n";
            echo "Service Name: " . $service->friendlyName . "\n";
            echo "---\n";
        }
    } catch (Exception $e) {
        echo "Could not fetch templates: " . $e->getMessage() . "\n";
        echo "This is normal for new WhatsApp Business accounts.\n";
    }
    
    echo "\n=== How to Fix the Delivery Issue ===\n";
    echo "1. **Immediate Solution**: Have the recipient send a message to +15558664742 first\n";
    echo "2. **Then**: You can send them messages for 24 hours\n";
    echo "3. **For Production**: Set up approved message templates in Twilio Console\n";
    echo "4. **Alternative**: Use the WhatsApp Business Manager to approve message templates\n";
    
    echo "\n=== Test the Opt-In Process ===\n";
    echo "1. From your phone (+447947768707), send a WhatsApp message to +15558664742\n";
    echo "2. Send any message like 'Hello' or 'Test'\n";
    echo "3. Then run the test script again - it should work!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

