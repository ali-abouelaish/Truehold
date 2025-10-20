# How to Fix Notification Configuration

## 📧 Email Configuration Fix

### Current Issue:
- Line 5 in env_config.txt has a typo: `crrm@truehold.co.uk` (extra 'r')
- Should be: `crm@truehold.co.uk`

### Steps to Fix:

1. **Open your `.env` file** (not env_config.txt)
2. **Update these email settings:**

```env
# Gmail SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=crm@truehold.co.uk
MAIL_PASSWORD=jwld qrlb ewzu uqf
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=crm@truehold.co.uk
MAIL_FROM_NAME="Truehold Group System"
```

3. **Clear Laravel config cache:**
```bash
php artisan config:clear
php artisan config:cache
```

## 📱 WhatsApp Configuration

### Current Status:
- ✅ Twilio credentials are working
- ✅ Admin notifications are working
- ⚠️ Client notifications fail due to invalid phone numbers

### To Fix Client Notifications:

1. **Check client phone numbers in database:**
```sql
SELECT id, full_name, phone_number FROM clients WHERE phone_number IS NOT NULL;
```

2. **Ensure phone numbers are:**
   - Valid WhatsApp numbers
   - Properly formatted (+44 for UK)
   - Registered with WhatsApp

3. **Test with a known WhatsApp number:**
   - Use your own WhatsApp number for testing
   - Update a test client's phone number

## 🧪 Testing the Fix

### After updating email config, test with:
```bash
php artisan tinker
```

Then run:
```php
Mail::raw('Test email', function($message) {
    $message->to('your-email@example.com')->subject('Test');
});
```

### For WhatsApp testing:
- Use the admin number (+447947768707) which is working
- Test with real WhatsApp numbers only

## 📍 File Locations

- **Main config**: `.env` file (in project root)
- **Backup config**: `env_config.txt` (reference only)
- **Laravel config**: `config/mail.php`, `config/services.php`
- **Notification code**: `app/Http/Controllers/RentalCodeController.php`
- **WhatsApp service**: `app/Services/WhatsAppService.php`

## 🎯 Priority Fixes

1. **Fix email typo** in `.env` file (line 5)
2. **Clear config cache** with `php artisan config:clear`
3. **Test email** with a simple test
4. **Validate client phone numbers** in database
5. **Test notifications** by creating a rental code

## ✅ Expected Results After Fix

- ✅ Email notifications will work
- ✅ Admin WhatsApp notifications will continue working
- ✅ Client WhatsApp notifications will work for valid numbers
- ✅ System will be fully functional
