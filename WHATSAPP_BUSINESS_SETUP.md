# WhatsApp Business API Setup Guide

## Current Issue: Messages Not Being Delivered

Your WhatsApp Business integration is working correctly (messages are being sent), but they're not being delivered due to WhatsApp Business API restrictions.

**Error Code 63016**: Message undelivered - requires proper setup for WhatsApp Business API.

## Solutions

### Option 1: Use Approved Message Templates (Recommended)

WhatsApp Business API requires approved message templates for first messages to new contacts.

#### Steps:
1. **Go to Twilio Console**: https://console.twilio.com/
2. **Navigate to**: Messaging > Senders > WhatsApp > Message Templates
3. **Create a new template** or use existing ones
4. **Common template types**:
   - `hello_world` - Simple greeting
   - `utility` - Informational messages
   - `marketing` - Promotional content

#### Example Template Message:
```php
$message = $client->messages->create(
    $to,
    [
        "from" => $whatsappNumber,
        "body" => "Hello! This is a message from Truehold Group CRM system."
    ]
);
```

### Option 2: Establish Session First (Quick Test)

To test immediately without templates:

1. **Send a message TO your business number**:
   - From your phone (+447947768707)
   - To your business number (+15558664742)
   - Send any message like "Hello" or "Test"

2. **Wait for confirmation** (you should receive an auto-reply)

3. **Test your application** - messages should now work for 24 hours

### Option 3: Check Message Templates in Twilio Console

1. **Login to Twilio Console**
2. **Go to**: Messaging > Senders > WhatsApp
3. **Click on your WhatsApp number** (+15558664742)
4. **Check "Message Templates" section**
5. **Look for approved templates** you can use

## Testing Commands

### Test with Template:
```bash
php test_whatsapp_template.php
```

### Check Message Status:
```bash
php check_latest_message.php
```

### Test Business Integration:
```bash
php test_whatsapp_business.php
```

## Common WhatsApp Business API Rules

1. **First Messages**: Must use approved templates
2. **Session Messages**: Free-form messages allowed for 24 hours after recipient responds
3. **Opt-in Required**: Recipients must consent to receive messages
4. **Rate Limits**: WhatsApp has strict rate limits
5. **Business Verification**: Your business must be verified with WhatsApp

## Troubleshooting

### If messages still don't work:

1. **Check Twilio Console** for approved templates
2. **Verify business verification** status
3. **Check account balance** in Twilio
4. **Review WhatsApp Business policies**
5. **Contact Twilio Support** with specific error codes

### Error Codes:
- **63016**: Message undelivered (most common)
- **63007**: Recipient not on WhatsApp
- **63017**: Recipient opted out

## Next Steps

1. **Try Option 2** (send message to business number first) for immediate testing
2. **Set up message templates** in Twilio Console for production use
3. **Implement opt-in process** for your users
4. **Test with approved templates** for reliable delivery
