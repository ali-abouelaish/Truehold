# Google Maps API Setup Guide

## 🚨 Current Issue
The map cannot load because the Google Maps API key is not configured. You'll see an error message: "Google Maps API key not configured."

## ✅ Solution

### 1. Get a Google Maps API Key
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the following APIs:
   - **Maps JavaScript API** (required)
   - **Places API** (for search functionality)
   - **Geocoding API** (for address lookup)
4. Go to "Credentials" → "Create Credentials" → "API Key"
5. Copy the generated API key
6. **Important**: Restrict the API key to your domain for security

### 2. Add the API Key to Your Environment
Add this line to your `.env` file`:
```bash
GOOGLE_MAPS_API_KEY=your_actual_api_key_here
```

### 3. Clear Configuration Cache
Run these commands:
```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Test the Map
1. Visit `/properties/map` in your browser
2. The map should now load with all properties displayed
3. You should see markers for each property with agent information

## 🔧 Troubleshooting

### If you still see errors:
1. **Check API key format**: Should be a long string like `AIzaSyB...`
2. **Verify APIs are enabled**: All three APIs must be enabled
3. **Check domain restrictions**: Make sure your domain is allowed
4. **Check billing**: Google Maps requires a billing account (free tier available)

### Common Error Messages:
- "API key not configured" → Add `GOOGLE_MAPS_API_KEY` to `.env`
- "Authentication failed" → Check API key is correct
- "Quota exceeded" → Check billing account

## Current Status
- ✅ Map query updated to include agent_name
- ✅ Agent names displayed in info windows
- ✅ Search functionality includes agent names
- ⚠️ Google Maps API key needs to be configured
- ✅ Tailwind CSS CDN warning fixed

## Testing
Once you add the API key, the map should work without errors and display:
- 100 properties with valid coordinates
- Agent names in property info windows
- Proper search functionality including agent names
