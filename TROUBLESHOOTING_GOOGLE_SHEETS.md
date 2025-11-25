# Troubleshooting Google Sheets Integration

## Issue: Rental codes not appearing in Google Sheet

### Step 1: Check Configuration

The error message `Google Sheets spreadsheet ID not configured` means you need to add these to your `.env` file:

```env
# Google Sheets Configuration
GOOGLE_SHEETS_SPREADSHEET_ID=your_spreadsheet_id_here
GOOGLE_SHEETS_SHEET_NAME=Sheet1
GOOGLE_SHEETS_CREDENTIALS_PATH=storage/app/google-credentials.json
```

### Step 2: Get Your Spreadsheet ID

1. Open your Google Sheet
2. Look at the URL: `https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit`
3. Copy the `SPREADSHEET_ID` part (the long string between `/d/` and `/edit`)
4. Add it to `.env` as: `GOOGLE_SHEETS_SPREADSHEET_ID=SPREADSHEET_ID`

### Step 3: Set Up Service Account Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a project or select existing one
3. Enable "Google Sheets API" in APIs & Services > Library
4. Go to APIs & Services > Credentials
5. Click "Create Credentials" > "Service Account"
6. Create the service account and download the JSON key file
7. Place the JSON file at: `storage/app/google-credentials.json`
8. Share your Google Sheet with the service account email (found in the JSON file as `client_email`)

### Step 4: Verify Setup

Run this command to check if configuration is loaded:
```bash
php artisan tinker --execute="echo config('services.google.sheets.spreadsheet_id') ? 'Configured' : 'NOT configured';"
```

### Step 5: Test with Existing Rental Code

If you want to manually sync an existing rental code:
```bash
php artisan tinker
```

Then run:
```php
$rentalCode = App\Models\RentalCode::find(38); // Use your rental code ID
$service = new App\Services\GoogleSheetsService();
$service->appendRentalCode($rentalCode);
```

### Common Issues

1. **"Spreadsheet ID not configured"** → Add `GOOGLE_SHEETS_SPREADSHEET_ID` to `.env`
2. **"Permission denied"** → Share the sheet with the service account email
3. **"Credentials not found"** → Place JSON file at `storage/app/google-credentials.json`
4. **"Service not initialized"** → Check credentials file path and format

### Check Logs

View recent logs for Google Sheets errors:
```bash
Get-Content storage\logs\laravel.log -Tail 100 | Select-String -Pattern "Google|Sheets"
```

