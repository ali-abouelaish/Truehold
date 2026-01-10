"""
Test script to verify the flag system is working correctly
"""
import gspread
import os
from google.oauth2.service_account import Credentials

# Setup credentials
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
PROJECT_ROOT = os.path.dirname(SCRIPT_DIR)
SERVICE_ACCOUNT_FILE = os.path.join(PROJECT_ROOT, "credentials", "service_account.json")

SCOPES = [
    "https://www.googleapis.com/auth/spreadsheets",
    "https://www.googleapis.com/auth/drive"
]

print("🔐 Authenticating with Google Sheets...")
creds = Credentials.from_service_account_file(SERVICE_ACCOUNT_FILE, scopes=SCOPES)
gc = gspread.authorize(creds)

print("📊 Opening Properties sheet...")
sheet = gc.open_by_key("1qkiVKv8HimkCznrxMvJVNSz-l9yNWkD0kQ2KYRlqV18").worksheet("Properties")

print("📋 Reading sheet headers...")
headers = sheet.row_values(1)
print(f"   Found {len(headers)} columns")
print(f"   Headers: {', '.join(headers[:10])}...")

# Check if flag columns exist
if 'flag' in headers:
    flag_col_index = headers.index('flag') + 1
    print(f"   ✅ 'flag' column found at position {flag_col_index}")
else:
    print(f"   ❌ 'flag' column NOT FOUND")

if 'flag_color' in headers:
    flag_color_col_index = headers.index('flag_color') + 1
    print(f"   ✅ 'flag_color' column found at position {flag_color_col_index}")
else:
    print(f"   ❌ 'flag_color' column NOT FOUND")

print("\n📊 Reading all property data...")
all_data = sheet.get_all_records()
print(f"   Found {len(all_data)} properties")

# Count properties with flags
flagged_count = 0
sample_flagged = []

for row in all_data:
    flag = row.get('flag', '')
    flag_color = row.get('flag_color', '')
    
    if flag or flag_color:
        flagged_count += 1
        if len(sample_flagged) < 5:
            sample_flagged.append({
                'url': row.get('url', 'N/A')[:60],
                'title': row.get('title', 'N/A')[:40],
                'flag': flag,
                'flag_color': flag_color
            })

print(f"\n📌 Properties with flags: {flagged_count} / {len(all_data)}")
if sample_flagged:
    print(f"\n🔍 Sample flagged properties:")
    for prop in sample_flagged:
        print(f"   - {prop['title']}")
        print(f"     Flag: '{prop['flag']}'")
        print(f"     Color: '{prop['flag_color']}'")
        print(f"     URL: {prop['url']}")
        print()
else:
    print("   ℹ️ No properties currently have flags set")

print("\n✅ Flag system test complete!")
print("\n📝 Instructions:")
print("   1. To add flags manually, use the admin interface at /admin/properties/flags")
print("   2. To add profile-level flags, edit the 3rd column in the Profiles sheet")
print("   3. Flags are preserved during daily scraper updates")
