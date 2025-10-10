# Gmail SMTP Troubleshooting Guide

## Current Issue: Authentication Failed

The Gmail SMTP authentication is failing with error:
```
535-5.7.8 Username and Password not accepted
```

## Possible Solutions:

### 1. **Verify App Password is Correct**
- The App Password `jwld qrlb ewzu uqf` might be incorrect
- Generate a new App Password from Gmail account settings

### 2. **Check Gmail Account Settings**
- Go to: https://myaccount.google.com/security
- Ensure 2-Factor Authentication is enabled
- Go to "App passwords" and generate a new one for "Mail"

### 3. **Try Different Gmail Settings**
Sometimes Gmail requires different SMTP settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=crm@truehold.co.uk
MAIL_PASSWORD=jwld qrlb ewzu uqf
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=crm@truehold.co.uk
MAIL_FROM_NAME="Truehold Group System"
```

### 4. **Alternative: Use Gmail API**
Instead of SMTP, we could use Gmail API which is more reliable.

### 5. **Check Gmail Account Status**
- Ensure the Gmail account `crm@truehold.co.uk` is active
- Check if there are any security restrictions
- Verify the account can send emails normally

## Current Configuration:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=crm@truehold.co.uk
MAIL_PASSWORD="jwld qrlb ewzu uqf"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=crm@truehold.co.uk
MAIL_FROM_NAME="Truehold Group System"
```

## Next Steps:
1. Verify the App Password is correct
2. Try port 465 with SSL instead of 587 with TLS
3. Generate a new App Password if needed
4. Check Gmail account security settings
