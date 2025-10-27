<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'phone_number' => env('TWILIO_PHONE_NUMBER'),
        'whatsapp_number' => env('TWILIO_WHATSAPP_NUMBER'),
        'messaging_service_sid' => env('TWILIO_MESSAGING_SERVICE_SID'),
        'test_whatsapp_number' => env('TEST_WHATSAPP_NUMBER'),
        'admin_whatsapp_number' => env('ADMIN_WHATSAPP_NUMBER', '+447947768707'),
        // Approved WhatsApp Content Template SID (HX...)
        'rental_template_sid' => env('TWILIO_RENTAL_TEMPLATE_SID', 'HXe7a5d7e1a64ec70a35cf674d9b0dd82d'),
        // Optional content language (e.g., en)
        'content_language' => env('TWILIO_CONTENT_LANGUAGE', 'en'),
    ],

];
