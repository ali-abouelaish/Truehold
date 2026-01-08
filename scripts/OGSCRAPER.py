import requests
from bs4 import BeautifulSoup
import pandas as pd
import time

# -----------------------------
# 1) Load profiles from public Google Sheet
# -----------------------------

google_sheet_csv = "https://docs.google.com/spreadsheets/d/1qkiVKv8HimkCznrxMvJVNSz-l9yNWkD0kQ2KYRlqV18/export?format=csv"

df_profiles = pd.read_csv(google_sheet_csv)

profiles = df_profiles.iloc[:, 0].tolist()
flags = df_profiles.iloc[:, 1].fillna("").tolist()

# -----------------------------
# 2) Scraper setup
# -----------------------------
base_url = "https://www.spareroom.co.uk"
headers = {"User-Agent": "Mozilla/5.0"}

all_results = []   # ← this is now your main array

# -----------------------------
# 3) Scrape each profile
# -----------------------------
for profile_url, flag in zip(profiles, flags):
    print(f"\n🔍 Scraping profile: {profile_url}")
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
                    "paying": flag
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
# Construct path to service account file
SERVICE_ACCOUNT_FILE = os.path.join(PROJECT_ROOT, "credentials", "service_account.json")

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

def scrape_listing_advanced(url,paying):
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

        # Photos (only inside photo-gallery containers)
        all_photo_urls = []

        # ✅ Main image container
        main_gallery = soup.select_one("dl.photo-gallery__main-image-wrapper")
        if main_gallery:
            main_links = main_gallery.find_all("a", href=re.compile(
                r"^https://photos2\.spareroom\.co\.uk/images/flatshare/listings/large/[0-9]+/[0-9]+/[0-9]+\.jpg$"
            ))
            for link in main_links:
                photo_url = link.get("href")
                if photo_url and photo_url not in all_photo_urls:
                    all_photo_urls.append(photo_url)

        # ✅ Thumbnail gallery container
        thumb_gallery = soup.select_one("div.photo-gallery__thumbnails")
        if thumb_gallery:
            thumb_links = thumb_gallery.find_all("a", href=re.compile(
                r"^https://photos2\.spareroom\.co\.uk/images/flatshare/listings/large/[0-9]+/[0-9]+/[0-9]+\.jpg$"
            ))
            for link in thumb_links:
                photo_url = link.get("href")
                if photo_url and photo_url not in all_photo_urls:
                    all_photo_urls.append(photo_url)

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
            "paying": paying

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

        if not url:
            continue

        # 🚫 Skip duplicate links inside listings list
        if url in seen_urls:
            print(f"🔁 Skipping duplicate in listings: {url}")
            continue

        seen_urls.add(url)

        print(f"Scraping {url}")
        data = scrape_listing_advanced(url, paying)
        if data:
            results.append(data)

        time.sleep(1)

    # Convert to DataFrame
    df_output = pd.DataFrame(results)

    # SAFETY CHECK
    if df_output.empty:
        print("❌ No listings scraped. Sheet not updated.")
        return

    # 1️⃣ Clear everything EXCEPT the header row
    sheet.batch_clear(["A2:ZZ"])

    # 2️⃣ Ensure headers match dataframe
    existing_headers = sheet.row_values(1)
    df_headers = df_output.columns.tolist()

    if existing_headers != df_headers:
        sheet.update("A1", [df_headers])

    # 3️⃣ Append new rows
    rows = df_output.astype(str).values.tolist()
    sheet.append_rows(rows, value_input_option="RAW")

    print(f"\n✅ Successfully uploaded {len(df_output)} listings to Sheet!")