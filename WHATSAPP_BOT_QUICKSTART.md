# WhatsApp Bot - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Configure Environment Variables

Add to your `.env` file:

```env
WASENDER_API_KEY=your_api_key_from_wasender_dashboard
WASENDER_API_BASE=https://wasenderapi.com
WASENDER_GROUP_JID=120363123456789012@g.us
```

### Step 2: Clear Configuration Cache

```bash
php artisan config:clear
```

### Step 3: Start Local Development

```bash
# Terminal 1: Start Laravel
php artisan serve

# Terminal 2: Start ngrok
ngrok http 8000
```

Copy the ngrok HTTPS URL (e.g., `https://abc123.ngrok.io`)

### Step 4: Configure WasenderAPI Webhook

1. Login to WasenderAPI dashboard
2. Go to Settings → Webhooks
3. Set webhook URL: `https://abc123.ngrok.io/api/wasender/webhook`
4. Enable "Incoming Messages"
5. Save

### Step 5: Test

Send a WhatsApp message to your WasenderAPI number.
The message should be forwarded to your group!

---

## 📋 How to Get Configuration Values

### Get WASENDER_API_KEY:
1. Login to WasenderAPI
2. Settings → API Settings
3. Copy API Key

### Get WASENDER_GROUP_JID:

**Option 1 - Via API:**
```bash
curl -X GET "https://wasenderapi.com/api/get-groups" \
  -H "Authorization: Bearer YOUR_API_KEY"
```

**Option 2 - Via Dashboard:**
1. Login to WasenderAPI
2. Groups section
3. Copy group JID (format: `120363...@g.us`)

---

## 📤 Sending Rental Codes to WhatsApp Group

Your bot can send new rental codes to your WhatsApp group automatically!

### Webhook URL for Rental Notifications:
```
https://abc123.ngrok.io/api/rental-codes/notify-group
```

This endpoint accepts rental code data and sends it to your WhatsApp group.

### Test Sending a Rental Code:

**PowerShell:**
```powershell
.\test-rental-webhook.ps1
```

**Bash:**
```bash
./test-rental-webhook.sh
```

The rental code will appear in your WhatsApp group with formatted details! 🎉

---

## 🧪 Test Endpoints

**Test incoming messages (forwards to group):**
```bash
curl https://abc123.ngrok.io/api/wasender/test
```

**Test rental notification (sends rental to group):**
```bash
curl https://abc123.ngrok.io/api/rental-codes/test-notify
```

You should see test messages in your WhatsApp group!

---

## 📊 View Logs

```bash
# View live logs
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log

# Search for bot activity
grep "WasenderAPI" storage/logs/laravel.log
```

---

## ⚠️ Troubleshooting

**Not working?** Check these:

1. ✅ Webhook URL is HTTPS (not HTTP)
2. ✅ Group JID format is correct (`...@g.us`)
3. ✅ API Key is valid
4. ✅ WasenderAPI session is active
5. ✅ Logs show webhook is being received

**View detailed logs:**
```bash
tail -50 storage/logs/laravel.log
```

---

## 🌐 Deploy to Production

1. Deploy your Laravel app to a server with HTTPS
2. Update `.env` with production values
3. Update WasenderAPI webhook URL to production domain
4. Test with `/api/wasender/test`

**Production webhook URL format:**
```
https://yourdomain.com/api/wasender/webhook
```

---

## 📖 Full Documentation

See `WHATSAPP_BOT_SETUP.md` for:
- Detailed setup instructions
- Security best practices
- Customization options
- Production deployment guide
- Advanced configuration

---

## ✅ Quick Verification Checklist

- [ ] Environment variables added to `.env`
- [ ] Config cache cleared
- [ ] Laravel server running
- [ ] ngrok tunnel active (for local dev)
- [ ] Webhook configured in WasenderAPI
- [ ] Test message sent successfully
- [ ] Message appears in group

**All done? You're ready to go! 🎉**

---

Need help? Check the logs:
```bash
tail -f storage/logs/laravel.log
```

