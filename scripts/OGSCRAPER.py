import requests
from bs4 import BeautifulSoup
import pandas as pd
import time
import sys
import os

# Fix Windows console encoding for emojis
if sys.platform == 'win32':
    # Try to set UTF-8 encoding
    try:
        sys.stdout.reconfigure(encoding='utf-8')
    except:
        # If that fails, just replace emojis with text
        pass

# -----------------------------
# 1) Load profiles from public Google Sheet
# -----------------------------

google_sheet_csv = "https://docs.google.com/spreadsheets/d/1qkiVKv8HimkCznrxMvJVNSz-l9yNWkD0kQ2KYRlqV18/export?format=csv"

df_profiles = pd.read_csv(google_sheet_csv)

profiles = df_profiles.iloc[:, 0].tolist()
paying_flags = df_profiles.iloc[:, 1].fillna("").tolist()
property_flags = df_profiles.iloc[:, 2].fillna("").tolist() if len(df_profiles.columns) > 2 else [""] * len(profiles)

# -----------------------------
# 2) Scraper setup
# -----------------------------
base_url = "https://www.spareroom.co.uk"
headers = {"User-Agent": "Mozilla/5.0"}

all_results = []   # ← this is now your main array

# -----------------------------
# 3) Scrape each profile
# -----------------------------
for profile_url, paying_flag, property_flag in zip(profiles, paying_flags, property_flags):
    print(f"\n🔍 Scraping profile: {profile_url}")
    if property_flag:
        print(f"   📌 Will apply flag: {property_flag}")
    offset = 0
    prev_page_links = set()

    while True:
        page_url = (
            f"{profile_url}?offset={offset}"
            if "?" not in profile_url
            else f"{profile_url}&offset={offset}"
        )

        try:
            response = requests.get(page_url, headers=headers, timeout=10)
            response.raise_for_status()
            soup = BeautifulSoup(response.text, "html.parser")

            listings = soup.find_all("a", class_="listing-card__link")
            if not listings:
                break

            current_page_links = set()

            for a in listings:
                href = a.get("href")
                if href:
                    full_url = href if href.startswith("http") else base_url + href
                    current_page_links.add(full_url)

            if current_page_links == prev_page_links:
                break

            for link in current_page_links:
                all_results.append({
                    "profile": profile_url,
                    "url": link,
                    "paying": paying_flag,
                    "profile_flag": property_flag
                })

            prev_page_links = current_page_links
            offset += 10
            time.sleep(1.5)

        except Exception as e:
            print(f"⚠️ Error fetching {page_url}: {e}")
            break

# -----------------------------
# 4) Deduplicate & prepare array
# -----------------------------

# Deduplicate by URL
seen = set()
listings = []

for item in all_results:
    if item["url"] not in seen:
        seen.add(item["url"])
        listings.append(item)

print(f"\n✅ Finished scraping {len(listings)} unique listings")

# `listings` is now your ARRAY — pass it to the next step




import requests
from bs4 import BeautifulSoup
import re
import time
import json
import gspread
import pandas as pd
import os
from google.oauth2.service_account import Credentials

# Get the directory where this script is located
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
# Get the project root (parent directory of scripts folder)
PROJECT_ROOT = os.path.dirname(SCRIPT_DIR)
# Construct path to service account file - try multiple locations
SERVICE_ACCOUNT_PATHS = [
    os.path.join(PROJECT_ROOT, "storage", "app", "google-credentials.json"),
    os.path.join(PROJECT_ROOT, "credentials", "service_account.json"),
    os.path.join(PROJECT_ROOT, "storage", "app", "service_account.json")
]

SERVICE_ACCOUNT_FILE = None
for path in SERVICE_ACCOUNT_PATHS:
    if os.path.exists(path):
        SERVICE_ACCOUNT_FILE = path
        print(f"Found credentials at: {path}")
        break

if not SERVICE_ACCOUNT_FILE:
    print("ERROR: Could not find Google service account credentials file!")
    print("Tried the following paths:")
    for path in SERVICE_ACCOUNT_PATHS:
        print(f"  - {path}")
    sys.exit(1)

SCOPES = [
    "https://www.googleapis.com/auth/spreadsheets",
    "https://www.googleapis.com/auth/drive"
]

creds = Credentials.from_service_account_file(
    SERVICE_ACCOUNT_FILE,
    scopes=SCOPES
)

gc = gspread.authorize(creds)


sheet = gc.open_by_key(
    "1qkiVKv8HimkCznrxMvJVNSz-l9yNWkD0kQ2KYRlqV18"
).worksheet("Properties")

print("Google Sheets connected successfully!")

def clean_text(text):
    """Clean and normalize text data (for titles, small fields)"""
    if not text or text == "N/A":
        return None
    text = re.sub(r'\s+', ' ', text.strip())  # remove extra whitespace
    text = re.sub(r'<[^>]+>', '', text)       # remove HTML tags
    if len(text) > 500:
        text = text[:500] + "..."
    return text

def extract_rich_text(elem):
    """Extract text while preserving emojis and newlines from <br> tags (for description only)"""
    if not elem:
        return None
    for br in elem.find_all("br"):
        br.replace_with("\n")
    text = elem.get_text()
    text = text.strip()
    return text if text else None

def extract_price(price_text):
    """Extract and normalize price information (convert pw → monthly if needed)"""
    if not price_text or price_text == "N/A":
        return None

    price_match = re.search(r'£?(\d+(?:,\d+)?(?:\.\d{2})?)', str(price_text))
    if price_match:
        price = price_match.group(1).replace(',', '')
        try:
            price = float(price)
            text_lower = price_text.lower()

            if "pw" in text_lower or "per week" in text_lower:
                # Convert weekly → monthly (average 52 weeks / 12 months)
                price = round(price * 52 / 12)

            else:
                # Assume price is already per month
                price = round(price)

            return int(price)
        except:
            return None
    return None


def extract_coordinates(lat_text, lon_text):
    """Extract and validate coordinates"""
    try:
        lat = float(lat_text) if lat_text and lat_text != "N/A" else None
        lon = float(lon_text) if lon_text and lon_text != "N/A" else None
        if lat is not None and (-90 <= lat <= 90):
            lat = round(lat, 8)
        else:
            lat = None
        if lon is not None and (-180 <= lon <= 180):
            lon = round(lon, 8)
        else:
            lon = None
        return lat, lon
    except:
        return None, None

def extract_feature_list(soup):
    """Extracts key-value pairs from feature-list <dl> blocks"""
    features = {}
    for dl in soup.find_all("dl", class_="feature-list"):
        keys = dl.find_all("dt", class_="feature-list__key")
        vals = dl.find_all("dd", class_="feature-list__value")

        for k, v in zip(keys, vals):
            key = clean_text(k.get_text()) if k else None
            val = clean_text(v.get_text()) if v else None

            # Handle tick/cross spans ("Yes"/"No")
            if v and v.find("span", class_="tick"):
                val = "Yes"
            elif v and v.find("span", class_="cross"):
                val = "No"

            if key:
                features[key] = val
    return features

def scrape_listing_advanced(url, paying, profile_flag=""):
    try:
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        }
        resp = requests.get(url, headers=headers)
        resp.raise_for_status()
        soup = BeautifulSoup(resp.text, "html.parser")
        html = resp.text
        if "The advertiser is not currently accepting applications" in html:
            print(f"🚫 Skipping {url} — advertiser not accepting applications.")
            return None

        # Title
        title = "N/A"
        title_elem = soup.find("h1") or soup.find("h2") or soup.find("title")
        if title_elem:
            title = clean_text(title_elem.get_text())

        # Agent / Landlord name
        agent_name = None
        agent_elem = soup.find("strong", class_="profile-photo__name")
        if agent_elem:
            agent_name = clean_text(agent_elem.get_text())

       # Extract location (always the 2nd <li> inside .key-features)
        location = None
        key_features = soup.find("ul", class_="key-features")
        if key_features:
         items = key_features.find_all("li", class_="key-features__feature")
         if len(items) >= 2:
          location = items[1].get_text(strip=True)  # ✅ "Devons Road"


        # Latitude / Longitude
        latitude, longitude = "N/A", "N/A"
        script_tags = soup.find_all("script")
        for script in script_tags:
            if script.string:
                script_content = script.string
                lat_match = re.search(r'latitude["\s]*:["\s]*"?([0-9.-]+)"?', script_content)
                lon_match = re.search(r'longitude["\s]*:["\s]*"?([0-9.-]+)"?', script_content)
                if lat_match:
                    latitude = lat_match.group(1)
                if lon_match:
                    longitude = lon_match.group(1)
                location_match = re.search(
                    r'location["\s]*:["\s]*{[^}]*latitude["\s]*:["\s]*"?([0-9.-]+)"?[^}]*longitude["\s]*:["\s]*"?([0-9.-]+)"?',
                    script_content
                )
                if location_match:
                    latitude, longitude = location_match.group(1), location_match.group(2)

        # Photos (collect ALL images from photo galleries)
        all_photo_urls = []

        # ✅ Method 1: Main image container
        main_gallery = soup.select_one("dl.photo-gallery__main-image-wrapper")
        if main_gallery:
            main_links = main_gallery.find_all("a", href=re.compile(
                r"^https://photos2\.spareroom\.co\.uk/images/flatshare/listings/large/[0-9]+/[0-9]+/[0-9]+\.jpg$"
            ))
            for link in main_links:
                photo_url = link.get("href")
                if photo_url and photo_url not in all_photo_urls:
                    all_photo_urls.append(photo_url)

        # ✅ Method 2: Thumbnail gallery container
        thumb_gallery = soup.select_one("div.photo-gallery__thumbnails")
        if thumb_gallery:
            thumb_links = thumb_gallery.find_all("a", href=re.compile(
                r"^https://photos2\.spareroom\.co\.uk/images/flatshare/listings/large/[0-9]+/[0-9]+/[0-9]+\.jpg$"
            ))
            for link in thumb_links:
                photo_url = link.get("href")
                if photo_url and photo_url not in all_photo_urls:
                    all_photo_urls.append(photo_url)

        # ✅ Method 3: Look for img tags with photo URLs
        photo_imgs = soup.find_all("img", src=re.compile(
            r"photos2\.spareroom\.co\.uk/images/flatshare/listings/"
        ))
        for img in photo_imgs:
            src = img.get("src", "")
            # Convert thumbnail/medium URLs to large URLs
            photo_url = re.sub(r"/\w+/(\d+/\d+/\d+\.jpg)", r"/large/\1", src)
            if "photos2.spareroom.co.uk" in photo_url and photo_url not in all_photo_urls:
                if not photo_url.startswith("http"):
                    photo_url = "https://" + photo_url.lstrip("/")
                all_photo_urls.append(photo_url)

        # ✅ Method 4: Look for data-src attributes (lazy loading)
        lazy_imgs = soup.find_all(attrs={"data-src": re.compile(
            r"photos2\.spareroom\.co\.uk/images/flatshare/listings/"
        )})
        for img in lazy_imgs:
            data_src = img.get("data-src", "")
            photo_url = re.sub(r"/\w+/(\d+/\d+/\d+\.jpg)", r"/large/\1", data_src)
            if "photos2.spareroom.co.uk" in photo_url and photo_url not in all_photo_urls:
                if not photo_url.startswith("http"):
                    photo_url = "https://" + photo_url.lstrip("/")
                all_photo_urls.append(photo_url)

        # ✅ Method 5: Check for carousel/slider containers
        carousel_containers = soup.select(".carousel, .slider, .photo-carousel, [class*='photo']")
        for container in carousel_containers:
            links = container.find_all("a", href=re.compile(
                r"photos2\.spareroom\.co\.uk.*\.jpg"
            ))
            for link in links:
                photo_url = link.get("href")
                if photo_url:
                    photo_url = re.sub(r"/\w+/(\d+/\d+/\d+\.jpg)", r"/large/\1", photo_url)
                    if not photo_url.startswith("http"):
                        photo_url = "https://" + photo_url.lstrip("/")
                    if photo_url not in all_photo_urls:
                        all_photo_urls.append(photo_url)

        print(f"📸 Found {len(all_photo_urls)} photos for {url}")
        
        first_photo_url = all_photo_urls[0] if all_photo_urls else None
        photo_count = len(all_photo_urls)
        all_photos = ", ".join(all_photo_urls) if all_photo_urls else None

        if photo_count == 0:
          print(f"🖼️ Skipping {url} — no images found.")
          return None

        # Price
        price = None
        price_selectors = [".price", ".rent", ".amount", "[class*='price']", "[class*='rent']", "[class*='amount']"]
        for selector in price_selectors:
            try:
                elements = soup.select(selector)
                for elem in elements:
                    text = elem.get_text(strip=True)
                    if '£' in text:
                        price = extract_price(text)
                        if price:
                            break
            except:
                continue

        # ✅ Description (preserve emojis + newlines)
        description = None
        detaildesc_elem = soup.find("p", class_="detaildesc")
        if detaildesc_elem:
            description = extract_rich_text(detaildesc_elem)
        else:
            feature_desc_body = soup.find("div", class_="feature__description-body")
            if feature_desc_body:
                description = extract_rich_text(feature_desc_body)
            else:
                desc_selectors = [
                    ".description", ".details", ".content",
                    "[class*='description']", "[class*='details']",
                    "[class*='content']", "p", ".listing-details"
                ]
                for selector in desc_selectors:
                    try:
                        elements = soup.select(selector)
                        for elem in elements:
                            text = extract_rich_text(elem)
                            if text and len(text) > 50:
                                description = text
                                break
                        if description:
                            break
                    except:
                        continue

        # Property type
        property_type = None
        type_keywords = ["room", "bedroom", "studio", "flat", "apartment", "house", "en-suite", "ensuite"]
        search_text = (str(title) + " " + str(description)).lower()
        for keyword in type_keywords:
            if keyword in search_text:
                property_type = keyword.title()
                break

        # ✅ Extract structured features
        features = extract_feature_list(soup)
        available_date = features.get("Available")
        min_term = features.get("Minimum term")
        max_term = features.get("Maximum term")
        deposit = features.get("Deposit")
        bills_included = features.get("Bills included?")
        furnishings = features.get("Furnishings")
        parking = features.get("Parking")
        garden = features.get("Garden/patio")
        broadband = features.get("Broadband included")
        housemates = features.get("# housemates")
        total_rooms = features.get("Total # rooms")
        smoker = features.get("Smoker?")
        pets = features.get("Any pets?")
        occupation = features.get("Occupation")
        gender = features.get("Gender")
        couples_ok = features.get("Couples OK?")
        smoking_ok = features.get("Smoking OK?")
        pets_ok = features.get("Pets OK?")
        pref_occupation = features.get("Occupation")  # new housemate occupation
        references = features.get("References?")
        min_age = features.get("Min age")
        max_age = features.get("Max age")

        # Final result dictionary
        lat, lon = extract_coordinates(latitude, longitude)
        result = {
            "url": url,
            "title": title,
            "agent_name": agent_name,
            "location": location,
            "latitude": lat,
            "longitude": lon,
            "status": "available",
            "price": price,
            "description": description,  # ✅ emojis + newlines preserved
            "property_type": property_type,
            "available_date": available_date,
            "min_term": min_term,
            "max_term": max_term,
            "deposit": deposit,
            "bills_included": bills_included,
            "furnishings": furnishings,
            "parking": parking,
            "garden": garden,
            "broadband": broadband,
            "housemates": housemates,
            "total_rooms": total_rooms,
            "smoker": smoker,
            "pets": pets,
            "occupation": occupation,
            "gender": gender,
            "couples_ok": couples_ok,
            "smoking_ok": smoking_ok,
            "pets_ok": pets_ok,
            "pref_occupation": pref_occupation,
            "references": references,
            "min_age": min_age,
            "max_age": max_age,
            "photo_count": photo_count,
            "first_photo_url": first_photo_url,
            "all_photos": all_photos,
            "photos": json.dumps(all_photo_urls) if all_photo_urls else None,
            "paying": paying,
            "profile_flag": profile_flag,
            "flag": "",  # ✅ Will be populated from profile or manual flags
            "flag_color": ""  # ✅ Will be populated from profile or manual flags
        }

        return result
    except Exception as e:
        print(f"Failed to scrape {url}: {e}")
        return None


def main(listings):
    results = []
    seen_urls = set()  # ✅ track URLs already processed in this run

    for item in listings:
        url = item.get("url")
        paying = item.get("paying", "")
        profile_flag = item.get("profile_flag", "")

        if not url:
            continue

        # 🚫 Skip duplicate links inside listings list
        if url in seen_urls:
            print(f"🔁 Skipping duplicate in listings: {url}")
            continue

        seen_urls.add(url)

        print(f"Scraping {url}")
        if profile_flag:
            print(f"   📌 Applying profile flag: {profile_flag}")
        data = scrape_listing_advanced(url, paying, profile_flag)
        if data:
            results.append(data)

        time.sleep(1)

    # Convert to DataFrame
    df_output = pd.DataFrame(results)

    # SAFETY CHECK
    if df_output.empty:
        print("❌ No listings scraped. Sheet not updated.")
        return

    # ========================================
    # PRESERVE EXISTING FLAGS
    # ========================================
    print("\n📌 Preserving existing flags...")
    
    # 1️⃣ Read existing data with flags
    existing_data = sheet.get_all_records()
    existing_headers = sheet.row_values(1)
    
    # Create a dictionary mapping URL -> (flag, flag_color)
    flag_map = {}
    if existing_data:
        for row in existing_data:
            url = row.get('url', '').strip()
            flag = row.get('flag', '')
            flag_color = row.get('flag_color', '')
            
            # Only store if URL exists and flag is set
            if url and (flag or flag_color):
                flag_map[url] = {
                    'flag': flag if flag else '',
                    'flag_color': flag_color if flag_color else ''
                }
    
    print(f"✓ Found {len(flag_map)} properties with existing flags")
    
    # 2️⃣ Add flag and flag_color columns to df_output if they don't exist
    if 'flag' not in df_output.columns:
        df_output['flag'] = ''
    if 'flag_color' not in df_output.columns:
        df_output['flag_color'] = ''
    
    # 3️⃣ Apply flags with priority: Manual > Profile > Empty
    manual_count = 0
    profile_count = 0
    new_count = 0
    
    for idx, row in df_output.iterrows():
        url = row.get('url', '').strip()
        profile_flag = row.get('profile_flag', '').strip()
        
        # Priority 1: Manual flags from sheet (preserved)
        if url in flag_map:
            df_output.at[idx, 'flag'] = flag_map[url]['flag']
            df_output.at[idx, 'flag_color'] = flag_map[url]['flag_color']
            manual_count += 1
            print(f"   📌 Preserved manual flag for {url[:50]}... : '{flag_map[url]['flag']}'")
        # Priority 2: Profile flags from agent
        elif profile_flag:
            df_output.at[idx, 'flag'] = profile_flag
            # You can set a default color here or leave empty
            # df_output.at[idx, 'flag_color'] = '#FFD700'  # Gold for profile flags
            profile_count += 1
            print(f"   🏷️ Applied profile flag for {url[:50]}... : '{profile_flag}'")
        else:
            new_count += 1
    
    print(f"✓ Applied {manual_count} manual flags (preserved)")
    print(f"✓ Applied {profile_count} profile flags (from agents)")
    print(f"✓ {new_count} properties with no flags")
    
    # Remove the profile_flag column before uploading (it's only used internally)
    if 'profile_flag' in df_output.columns:
        df_output = df_output.drop(columns=['profile_flag'])
    
    # ========================================
    # UPDATE SHEET
    # ========================================
    
    # 4️⃣ Clear everything EXCEPT the header row
    sheet.batch_clear(["A2:ZZ"])

    # 5️⃣ Ensure headers match dataframe
    df_headers = df_output.columns.tolist()
    
    print(f"\n📋 Columns to be written to sheet ({len(df_headers)}):")
    print(f"   {', '.join(df_headers)}")
    
    # Verify flag columns exist
    if 'flag' in df_headers and 'flag_color' in df_headers:
        print(f"   ✅ Flag columns present in data")
        # Show sample of flag data
        flagged_props = df_output[df_output['flag'] != '']
        if len(flagged_props) > 0:
            print(f"   📊 Properties with flags: {len(flagged_props)}")
            for idx, prop in flagged_props.head(3).iterrows():
                print(f"      - {prop.get('title', 'N/A')[:40]}: flag='{prop.get('flag', '')}' color='{prop.get('flag_color', '')}'")
    else:
        print(f"   ⚠️ WARNING: Flag columns missing from data!")

    if existing_headers != df_headers:
        sheet.update("A1", [df_headers])
        print(f"✓ Updated headers: {df_headers}")

    # 6️⃣ Append new rows with preserved flags
    rows = df_output.astype(str).values.tolist()
    sheet.append_rows(rows, value_input_option="RAW")

    print(f"\n✅ Successfully uploaded {len(df_output)} listings to Sheet with preserved flags!")


# Call main() to scrape each individual listing
if __name__ == "__main__":
    main(listings)