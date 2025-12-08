# Google Sheets Properties Integration Setup

This application now reads properties from Google Sheets instead of the MySQL database. The structure remains the same, only the data source has changed.

## Prerequisites

1. A Google Cloud Project with the Google Sheets API enabled
2. A service account with credentials (can reuse the same one used for rental codes)
3. A Google Sheet with properties data that the service account has access to

## Setup Instructions

### Step 1: Create/Prepare Your Google Sheet

1. Create a new Google Sheet or use an existing one
2. Add a header row with column names matching the property fields (case-insensitive)
3. Add your property data rows below the header

**Recommended Column Headers:**
- `id` (or use row number)
- `link` or `url`
- `title`
- `location`
- `latitude` or `lat`
- `longitude` or `lng` or `lon`
- `price`
- `description`
- `property_type` or `type`
- `available_date` or `availability`
- `min_term` or `minimum_term`
- `max_term` or `maximum_term`
- `deposit`
- `bills_included`
- `furnishings`
- `parking`
- `garden` or `garden_patio`
- `broadband` or `broadband_included`
- `management_company`
- `agent_name`
- `agent_id`
- `paying`
- `status`
- `photos` (JSON array or comma-separated URLs)
- `all_photos` (comma-separated URLs)
- And any other property fields you need

### Step 2: Share Google Sheet with Service Account

1. Open your Google Sheet
2. Click the "Share" button
3. Add the service account email (found in your JSON credentials file as `client_email`)
4. Give it "Viewer" or "Editor" permissions (Viewer is sufficient for reading)
5. Copy the Spreadsheet ID from the URL:
   - The URL looks like: `https://docs.google.com/spreadsheets/d/SPREADSHEET_ID/edit`
   - Copy the `SPREADSHEET_ID` part

### Step 3: Configure Environment Variables

Add the following to your `.env` file:

```env
# Google Sheets Properties Configuration
GOOGLE_PROPERTIES_SPREADSHEET_ID=your_properties_spreadsheet_id_here
GOOGLE_PROPERTIES_SHEET_NAME=Properties
GOOGLE_PROPERTIES_CACHE_TIMEOUT=300

# Optional: If using different credentials than rental codes
GOOGLE_PROPERTIES_CREDENTIALS_PATH=storage/app/google-credentials.json
GOOGLE_PROPERTIES_CREDENTIALS_JSON={"type":"service_account",...}
```

**Note:** If you don't specify `GOOGLE_PROPERTIES_CREDENTIALS_PATH` or `GOOGLE_PROPERTIES_CREDENTIALS_JSON`, the system will use the same credentials as configured for rental codes (`GOOGLE_SHEETS_CREDENTIALS_PATH` or `GOOGLE_SHEETS_CREDENTIALS_JSON`).

### Step 4: Verify Setup

1. Clear the application cache:
   ```bash
   php artisan cache:clear
   ```

2. Test the integration by visiting the properties page in your application

3. Check the Laravel logs (`storage/logs/laravel.log`) for any errors

## Configuration Options

### Cache Timeout

The `GOOGLE_PROPERTIES_CACHE_TIMEOUT` setting controls how long property data is cached (in seconds). Default is 300 seconds (5 minutes). This helps reduce API calls to Google Sheets.

To refresh the cache manually, you can call:
```php
app(\App\Services\PropertyGoogleSheetsService::class)->clearCache();
```

### Sheet Name

The `GOOGLE_PROPERTIES_SHEET_NAME` setting specifies which sheet/tab to read from. Default is "Properties". Make sure this matches the exact name of your sheet tab.

## Column Mapping

The service automatically maps common column name variations to property fields:

- `link` or `url` → `link`
- `lat` or `latitude` → `latitude`
- `lng`, `lon`, or `longitude` → `longitude`
- `type` or `property_type` → `property_type`
- `availability` or `available_date` → `available_date`
- `minimum_term` or `min_term` → `min_term`
- `maximum_term` or `max_term` → `max_term`
- `garden_patio` or `garden` → `garden`
- `broadband_included` or `broadband` → `broadband`

## Data Format

### Photos

Photos can be stored in two formats:

1. **JSON Array** (recommended):
   ```json
   ["https://example.com/photo1.jpg", "https://example.com/photo2.jpg"]
   ```

2. **Comma-separated URLs**:
   ```
   https://example.com/photo1.jpg, https://example.com/photo2.jpg
   ```

### Coordinates

- `latitude`: Decimal number between -90 and 90
- `longitude`: Decimal number between -180 and 180

### Status

Common values: `available`, `rented`, `unavailable`, `on_hold`

### Boolean Fields

Fields like `updatable`, `couples_ok`, etc. can be:
- `true`, `1`, `yes` → true
- `false`, `0`, `no` → false

## Troubleshooting

### Properties not showing

1. Check that `GOOGLE_PROPERTIES_SPREADSHEET_ID` is set correctly
2. Verify the service account has access to the sheet
3. Check the sheet name matches `GOOGLE_PROPERTIES_SHEET_NAME`
4. Review Laravel logs for errors

### Cache issues

If you update the Google Sheet and changes don't appear:
1. Clear the cache: `php artisan cache:clear`
2. Or reduce `GOOGLE_PROPERTIES_CACHE_TIMEOUT` for more frequent updates

### Column mapping issues

If certain fields aren't being read:
1. Check that column headers match the expected names (case-insensitive)
2. Review the `getHeaderMap()` method in `PropertyGoogleSheetsService.php` for supported column names

## Performance Considerations

- Properties are cached for 5 minutes by default to reduce API calls
- For large sheets (1000+ rows), consider increasing cache timeout
- The service reads all properties at once, so very large sheets may be slow

## Migration from Database

If you're migrating from database to Google Sheets:

1. Export your properties from the database to CSV
2. Import the CSV into Google Sheets
3. Ensure column headers match the expected format
4. Update your `.env` file with the new configuration
5. Clear the cache and test

The application will automatically use Google Sheets once configured. No code changes needed in views or other parts of the application.
