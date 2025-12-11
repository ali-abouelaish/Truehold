# WhatsApp Bot Setup Guide - WasenderAPI Integration

This guide will help you set up and deploy the WhatsApp bot that forwards incoming messages to a WhatsApp group.

---

## 📋 Overview

The bot works as follows:
1. Receives incoming WhatsApp messages via WasenderAPI webhook
2. Extracts sender information and message content
3. Forwards the message to a designated WhatsApp group
4. Logs all activities for monitoring and debugging

---

## 🔧 Prerequisites

- Laravel 10+ application (✅ Already set up)
- WasenderAPI account with an active session
- A WhatsApp group where messages will be forwarded
- ngrok (for local testing) or a production server with HTTPS

---

## ⚙️ Configuration

### Step 1: Add Environment Variables

Add the following variables to your `.env` file:

```env
# WasenderAPI Configuration
WASENDER_API_KEY=your_api_key_here
WASENDER_API_BASE=https://wasenderapi.com
WASENDER_GROUP_JID=your_group_jid_here
```

**How to get these values:**

1. **WASENDER_API_KEY**: 
   - Login to your WasenderAPI dashboard
   - Go to Settings → API Settings
   - Copy your API Key

2. **WASENDER_API_BASE**: 
   - Default: `https://wasenderapi.com`
   - If you have a custom domain or self-hosted instance, use that URL

3. **WASENDER_GROUP_JID**:
   - See "Getting Group JID" section below

---

## 📱 Getting the Group JID

The Group JID is the unique identifier for your WhatsApp group.

### Method 1: Using WasenderAPI Dashboard
1. Login to WasenderAPI dashboard
2. Go to Groups section
3. Find your group and copy the JID
4. It should look like: `120363123456789012@g.us`

### Method 2: Using API Call
Make a GET request to WasenderAPI:
```bash
curl -X GET "https://wasenderapi.com/api/get-groups" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

Look for your group in the response and copy the `id` field (this is the JID).

### Method 3: From Webhook Payload
1. Send a message to the group manually
2. Check your Laravel logs for the webhook payload
3. Look for `remoteJid` field in the logs - this is your group JID

---

## 🚀 Local Development Setup

### 1. Install ngrok

Download and install ngrok from https://ngrok.com/download

### 2. Start Your Laravel Application

```bash
php artisan serve
# Application will run on http://127.0.0.1:8000
```

### 3. Create ngrok Tunnel

Open a new terminal and run:
```bash
ngrok http 8000
```

You'll see output like:
```
Forwarding   https://abc123.ngrok.io -> http://localhost:8000
```

Copy the HTTPS URL (e.g., `https://abc123.ngrok.io`)

### 4. Configure WasenderAPI Webhook

1. Login to WasenderAPI dashboard
2. Go to Settings → Webhooks
3. Set the webhook URL to: `https://abc123.ngrok.io/api/wasender/webhook`
4. Enable webhook for "Incoming Messages"
5. Save the configuration

### 5. Test the Bot

Send a WhatsApp message to your WasenderAPI number. You should see:
- The message logged in `storage/logs/laravel.log`
- The message forwarded to your group

#### Quick Test Endpoint

You can also test the group messaging directly:
```bash
curl -X GET "https://abc123.ngrok.io/api/wasender/test"
```

This will send a test message to your configured group.

---

## 🌐 Production Deployment

### Option 1: Deploy to VPS/Dedicated Server

1. **Set up your server with HTTPS**
   ```bash
   # Install SSL certificate (Let's Encrypt example)
   sudo certbot --nginx -d yourdomain.com
   ```

2. **Update WasenderAPI webhook URL**
   ```
   https://yourdomain.com/api/wasender/webhook
   ```

3. **Set environment variables**
   - Update `.env` file on production server
   - Run `php artisan config:clear` after changes

4. **Set up supervisor for queue workers** (optional)
   ```bash
   sudo apt-get install supervisor
   ```

### Option 2: Deploy to Laravel Forge

1. Create a new site on Forge
2. Deploy your application
3. Add environment variables in Forge dashboard
4. Update WasenderAPI webhook URL to your Forge domain

### Option 3: Deploy to Heroku

1. Create Heroku app:
   ```bash
   heroku create your-app-name
   ```

2. Set environment variables:
   ```bash
   heroku config:set WASENDER_API_KEY=your_key_here
   heroku config:set WASENDER_API_BASE=https://wasenderapi.com
   heroku config:set WASENDER_GROUP_JID=your_group_jid_here
   ```

3. Deploy:
   ```bash
   git push heroku main
   ```

4. Update webhook URL: `https://your-app-name.herokuapp.com/api/wasender/webhook`

---

## 🔍 Monitoring & Debugging

### View Logs

All webhook activity is logged to Laravel's daily log:

```bash
# View live logs
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log

# Search for webhook activity
grep "WasenderAPI" storage/logs/laravel-*.log
```

### Log Entries Include:

1. **Incoming webhooks**: Full payload received
2. **Sender extraction**: Who sent the message
3. **Message forwarding**: Success/failure status
4. **API responses**: WasenderAPI responses
5. **Errors**: Any errors that occur

### Common Issues & Solutions

#### Issue: Messages not being forwarded

**Solution:**
- Check logs: `storage/logs/laravel.log`
- Verify `WASENDER_GROUP_JID` is correct
- Ensure `WASENDER_API_KEY` is valid
- Check WasenderAPI session is active

#### Issue: Webhook not receiving messages

**Solution:**
- Verify webhook URL in WasenderAPI dashboard
- Ensure HTTPS is working (not HTTP)
- Check ngrok tunnel is running (for local dev)
- Verify webhook is enabled for "Incoming Messages"

#### Issue: Bot loops (message keeps forwarding)

**Solution:**
- The bot already ignores messages with `fromMe: true`
- Check logs to confirm this filter is working
- Verify you're not in the group with multiple numbers

#### Issue: Configuration not loading

**Solution:**
```bash
# Clear config cache
php artisan config:clear

# Clear all caches
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 🎯 Testing Checklist

Before going live, test these scenarios:

- [ ] Send a text message → Should forward to group
- [ ] Send an empty message → Should be ignored gracefully
- [ ] Send a message from group → Should not create a loop
- [ ] Bot sends test message → `/api/wasender/test` works
- [ ] Check logs → All activity is logged
- [ ] Error handling → Invalid webhooks don't crash the app

---

## 🔐 Security Best Practices

1. **Protect test endpoint in production**
   ```php
   // In routes/api.php, add middleware:
   Route::get('/wasender/test', [WasenderWebhookController::class, 'test'])
       ->middleware('auth:sanctum');
   ```

2. **Validate webhook source** (optional)
   - Add IP whitelist middleware
   - Verify webhook signature if WasenderAPI provides it

3. **Rate limiting** (optional)
   ```php
   Route::post('/wasender/webhook', [WasenderWebhookController::class, 'handle'])
       ->middleware('throttle:60,1'); // 60 requests per minute
   ```

4. **Environment variables**
   - Never commit `.env` file
   - Use secure environment variable management in production

---

## 📊 Customization Options

### Custom Message Format

Edit `formatMessage()` in `WasenderWebhookController.php`:

```php
protected function formatMessage(string $sender, string $message): string
{
    // Option 1: Simple format
    return "{$sender}: {$message}";
    
    // Option 2: With emoji and formatting (current)
    return "📱 *{$sender}*:\n{$message}";
    
    // Option 3: With timestamp
    $timestamp = now()->format('H:i');
    return "📱 *{$sender}* [{$timestamp}]:\n{$message}";
    
    // Option 4: With custom prefix
    return "[Forwarded] From: {$sender}\n\n{$message}";
}
```

### Filter Specific Messages

Add filtering logic in `handle()` method:

```php
// Ignore commands (messages starting with /)
if (str_starts_with($messageBody, '/')) {
    return response()->json(['success' => true, 'message' => 'Command ignored']);
}

// Only forward messages with specific keywords
if (!str_contains(strtolower($messageBody), 'important')) {
    return response()->json(['success' => true, 'message' => 'Non-important message ignored']);
}
```

### Forward to Multiple Groups

Modify `sendToGroup()` to loop through multiple group JIDs:

```php
// In .env add:
WASENDER_GROUP_JIDS=group1_jid,group2_jid,group3_jid

// In controller:
protected function sendToMultipleGroups(string $text): array
{
    $groupJids = explode(',', config('services.wasender.group_jid'));
    $results = [];
    
    foreach ($groupJids as $groupJid) {
        $results[] = $this->sendToGroup($text, trim($groupJid));
    }
    
    return $results;
}
```

---

## 📝 API Endpoints

| Endpoint | Method | Description | Auth Required |
|----------|--------|-------------|---------------|
| `/api/wasender/webhook` | POST | Receive incoming messages | No |
| `/api/wasender/test` | GET | Send test message to group | No (recommended to add) |

---

## 🆘 Support

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify WasenderAPI dashboard for session status
3. Test webhook with curl:
   ```bash
   curl -X POST https://your-domain.com/api/wasender/webhook \
     -H "Content-Type: application/json" \
     -d '{
       "key": {
         "fromMe": false,
         "cleanedSenderPn": "1234567890",
         "remoteJid": "1234567890@s.whatsapp.net"
       },
       "data": {
         "messages": {
           "messageBody": "Test message"
         }
       }
     }'
   ```

---

## ✅ Production Checklist

- [ ] Environment variables configured
- [ ] Webhook URL set in WasenderAPI dashboard
- [ ] HTTPS enabled
- [ ] Logs are being written
- [ ] Test message sent successfully
- [ ] Real message forwarded successfully
- [ ] Error handling tested
- [ ] Test endpoint secured or removed
- [ ] Monitoring/alerting set up (optional)

---

## 🔄 Maintenance

### Regular Tasks

1. **Monitor logs weekly**
   - Check for errors
   - Verify messages are being forwarded

2. **Update dependencies**
   ```bash
   composer update
   ```

3. **Backup configuration**
   - Keep `.env` values documented securely

4. **Test after updates**
   - Send test messages after Laravel updates
   - Verify WasenderAPI integration still works

---

## 📄 License

This WhatsApp bot integration is part of your Laravel application.

---

**Last Updated:** December 2025
**Laravel Version:** 10+
**WasenderAPI Version:** Latest

