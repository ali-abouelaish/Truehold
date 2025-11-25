# Google Sheets Integration Setup

This application automatically syncs new rental codes to a Google Sheet when they are created.

## Prerequisites

1. A Google Cloud Project with the Google Sheets API enabled
2. A service account with credentials
3. A Google Sheet that the service account has access to

## Setup Instructions

### Step 1: Create a Google Cloud Project and Enable Sheets API

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the Google Sheets API:
   - Navigate to "APIs & Services" > "Library"
   - Search for "Google Sheets API"
   - Click "Enable"

### Step 2: Create a Service Account

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "Service Account"
3. Fill in the service account details and click "Create"
4. Skip role assignment (optional) and click "Done"
5. Click on the created service account
6. Go to the "Keys" tab
7. Click "Add Key" > "Create new key"
8. Choose "JSON" format and download the key file

### Step 3: Share Google Sheet with Service Account

1. Open your Google Sheet (or create a new one)
2. Click the "Share" button
3. Add the service account email (found in the JSON file as `client_email`)
4. Give it "Editor" permissions
5. Copy the Spreadsheet ID from the URL:
   - The URL looks like: `https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit`
   - Copy the `SPREADSHEET_ID` part

### Step 4: Configure Environment Variables

Add the following to your `.env` file:

```env
# Google Sheets Configuration
GOOGLE_SHEETS_SPREADSHEET_ID=your_spreadsheet_id_here
GOOGLE_SHEETS_SHEET_NAME=Sheet1
GOOGLE_SHEETS_CREDENTIALS_PATH=storage/app/google-credentials.json
```

**Option 1: Using Credentials File Path**
1. Place the downloaded JSON credentials file in `storage/app/google-credentials.json`
2. Set `GOOGLE_SHEETS_CREDENTIALS_PATH=storage/app/google-credentials.json`

**Option 2: Using JSON String (Alternative)**
1. Copy the entire contents of the JSON credentials file
2. Add to `.env` as a single line (escape quotes properly):
   ```env
   GOOGLE_SHEETS_CREDENTIALS_JSON='{"type":"service_account","project_id":"...","private_key_id":"...","private_key":"...","client_email":"...","client_id":"...","auth_uri":"...","token_uri":"...","auth_provider_x509_cert_url":"...","client_x509_cert_url":"..."}'
   ```

### Step 5: Test the Integration

1. Create a new rental code in the application
2. Check your Google Sheet - a new row should appear with the rental code data
3. The first row will automatically contain headers if the sheet is empty

## Sheet Structure

The following columns will be created/used:
- Rental Code
- Rental Date
- Client Name
- Client Email
- Client Phone
- Property
- Licensor
- Consultation Fee
- Payment Method
- Status
- Rental Agent
- Marketing Agent
- Client Count
- Notes
- Created At

## Troubleshooting

### Error: "Failed to initialize Google Sheets client"
- Check that the credentials file path is correct
- Verify the JSON credentials are valid
- Ensure the Google Sheets API is enabled in your Google Cloud project

### Error: "Permission denied"
- Make sure you've shared the Google Sheet with the service account email
- Verify the service account has "Editor" permissions

### No data appearing in the sheet
- Check the application logs for detailed error messages
- Verify the Spreadsheet ID is correct
- Ensure the sheet name matches (default is "Sheet1")

## Security Notes

- Never commit the credentials JSON file to version control
- Add `storage/app/google-credentials.json` to `.gitignore`
- Keep your service account credentials secure
- Regularly rotate credentials if needed

