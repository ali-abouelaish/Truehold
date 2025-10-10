# Email Configuration for Invoice Notifications

## Current Email Settings

The invoice system is currently configured to send emails from:
- **From Address**: `crm@truehold.co.uk`
- **From Name**: `Truehold Group System`
- **To Address**: `board@truehold.co.uk`

## Required .env Configuration

To enable actual email sending (instead of just logging), update your `.env` file with these settings:

### Gmail SMTP Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=crm@truehold.co.uk
MAIL_PASSWORD=jwld qrlb ewzu uqf
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=crm@truehold.co.uk
MAIL_FROM_NAME="Truehold Group System"
```

### Option 2: Using a Service Provider
For production, consider using:
- **Mailgun**: `MAIL_MAILER=mailgun`
- **SendGrid**: `MAIL_MAILER=smtp` with SendGrid SMTP settings
- **Amazon SES**: `MAIL_MAILER=ses`

## Current Status
- ✅ Email template created
- ✅ Invoice controller updated
- ✅ From address set to `noreply@truehold.co.uk`
- ⚠️ **Action Required**: Update `.env` file to enable actual email sending

## Testing
To test the email functionality:
1. Update your `.env` file with proper SMTP settings
2. Create a test invoice
3. Check if the email is sent to `board@truehold.co.uk`

## Email Content
The email includes:
- Invoice number and details
- Agent name who generated the invoice
- PDF attachment of the invoice
- Professional formatting
