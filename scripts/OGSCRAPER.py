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

# HTTP session with retry/backoff and a realistic User-Agent
from requests.adapters import HTTPAdapter
try:
    from urllib3.util.retry import Retry
except ImportError:  # pragma: no cover
    from requests.packages.urllib3.util.retry import Retry


def _make_session():
    s = requests.Session()
    retry = Retry(
        total=3,
        backoff_factor=1.5,
        status_forcelist=[429, 500, 502, 503, 504],
        allowed_methods=frozenset(["GET"]),
    )
    adapter = HTTPAdapter(max_retries=retry, pool_connections=10, pool_maxsize=10)
    s.mount("https://", adapter)
    s.mount("http://", adapter)
    s.headers.update({
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 "
                      "(KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        "Accept-Language": "en-GB,en;q=0.7,en-US;q=0.5",
    })
    return s


SESSION = _make_session()

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
            response = SESSION.get(page_url, timeout=15)
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










def parse_money_to_int(text):
    """Extract £ number from text, return int (e.g. '£950.00' -> 950)."""
    if not text:
        return None
    m = re.search(r"£\s*([0-9][0-9,]*)(?:\.\d{2})?", str(text))
    if not m:
        return None
    return int(m.group(1).replace(",", ""))

def normalize_room_type(text):
    """Normalize room type to single/double/ensuite/studio/etc."""
    if not text:
        return None
    t = text.strip().lower()
    # SpareRoom often uses: single, double, ensuite, twin, studio
    if "en-suite" in t or "ensuite" in t or "en suite" in t:
        return "ensuite"
    if "single" in t:
        return "single"
    if "double" in t:
        return "double"
    if "studio" in t:
        return "studio"
    return t  # fallback

def extract_room_options(soup, max_rooms=6):
    """
    Extract per-room options from <section class="feature--price_room_only">.
    Returns list like:
    [{"room_index":1,"price_pcm":950,"room_type":"double"}, ...]
    """
    rooms = []
    dl = soup.select_one("section.feature--price_room_only dl.feature-list")
    if not dl:
        return rooms

    dts = dl.find_all("dt", class_="feature-list__key")
    dds = dl.find_all("dd", class_="feature-list__value")

    for i, (dt, dd) in enumerate(zip(dts, dds), start=1):
        if i > max_rooms:
            break
        price_text = dt.get_text(" ", strip=True) if dt else ""
        room_type_text = dd.get_text(" ", strip=True) if dd else ""

        # Use extract_price so pw → pcm conversion is applied for multi-room properties
        room_price_pcm = extract_price(price_text) if price_text else None
        if room_price_pcm is None:
            room_price_pcm = parse_money_to_int(price_text)

        rooms.append({
            "room_index": i,
            "price_pcm": room_price_pcm,
            "room_type": normalize_room_type(room_type_text),
        })
    return rooms

def extract_room_deposits(soup):
    """
    Extract deposits like:
      - Deposit (Room 1)
      - Security deposit (Room 2)
    Also extract generic:
      - Deposit
      - Security deposit
    Returns:
      (room_deposits_dict, generic_deposit_int_or_none)
    """
    room_deposits = {}
    generic_deposit = None

    extra = soup.select_one("section.feature--extra-cost dl.feature-list")
    if not extra:
        return room_deposits, generic_deposit

    keys = extra.find_all("dt", class_="feature-list__key")
    vals = extra.find_all("dd", class_="feature-list__value")

    for k, v in zip(keys, vals):
        ktxt = k.get_text(" ", strip=True) if k else ""
        vtxt = v.get_text(" ", strip=True) if v else ""

        k_lower = ktxt.lower()

        # Per-room: (security )deposit (room X)
        m = re.search(r"(security\s*)?deposit\s*\(room\s*(\d+)\)", k_lower, re.IGNORECASE)
        if m:
            room_idx = int(m.group(2))
            room_deposits[room_idx] = parse_money_to_int(vtxt)
            continue

        # Generic: Deposit / Security deposit (no room number)
        if "deposit" in k_lower:
            # prefer security deposit if multiple show up
            parsed = parse_money_to_int(vtxt)
            if parsed is not None:
                if generic_deposit is None:
                    generic_deposit = parsed
                elif "security" in k_lower:
                    generic_deposit = parsed

    return room_deposits, generic_deposit







def extract_whole_property_price(soup):
    """
    Find price from:
      <h3 class="feature__heading">£2,500 pcm <small>(whole property)</small></h3>
    Returns int price_pcm or None
    """
    for h in soup.select("h3.feature__heading"):
        txt = h.get_text(" ", strip=True)
        if "(whole property)" in txt.lower() or "whole property" in txt.lower():
            # Use your existing extract_price (handles commas and pw conversion)
            p = extract_price(txt)
            if p:
                return int(p)
    return None

def detect_whole_property(soup):
    """
    Detect whole property via '(whole property)' marker or 'This ad is for a ...' paragraph.
    Returns bool.
    """
    # strong marker
    for h in soup.select("h3.feature__heading"):
        if "whole property" in h.get_text(" ", strip=True).lower():
            return True

    # fallback: paragraph text
    for p in soup.select("p.feature__paragraph"):
        t = p.get_text(" ", strip=True).lower()
        if "this ad is for" in t and ("studio" in t or "bed" in t or "flat" in t or "apartment" in t or "house" in t):
            # not perfect, but usually whole property ads use this pattern
            return True

    return False
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


def detect_property_type_from_key_features(soup):
    ul = soup.find("ul", class_="key-features")
    if not ul:
        return None, False

    items = ul.find_all("li", class_="key-features__feature")
    if not items:
        return None, False

    first = items[0].get_text(" ", strip=True).lower()

    # Anything "*share*" means room in shared property
    if "share" in first:
        return "Room", False

    # Anything "*to rent*" means whole property
    if "to rent" in first:
        return "Flat", True

    return None, False
 


# -----------------------------
# Canonical data extraction from embedded _sr JS object
# -----------------------------
_UK_POSTCODE_AREA = r"[A-Z]{1,2}\d[A-Z\d]?"
_DISTANCE_RE = re.compile(
    r"(\d+(?:\.\d+)?)\s*(minutes?|mins?|miles?|mi|km|metres?|meters?|m)\b",
    re.IGNORECASE,
)
_TRANSPORT_KEYWORDS = (
    "underground", "tube", "overground", "national-rail", "national_rail",
    "rail", "dlr", "bus", "tram", "ferry",
)


def extract_sr_data(html):
    """Pull useful fields from the embedded _sr JS object and surrounding script vars."""
    data = {}

    # Static string fields under _sr.ecommerce...products[0]
    for field in ("variant", "ad_verified", "ad_profile_photo", "ad_pics",
                  "ad_video", "dimension11", "dimension12", "dimension13",
                  "dimension22"):
        m = re.search(rf"\b{field}\s*:\s*'([^']*)'", html)
        if m:
            data[field] = m.group(1)

    # accommodation type ('room', 'studio', etc.)
    acc = re.search(r"var\s+accomodationType\s*=\s*'([^']*)'", html)
    if acc:
        data["accommodation_type"] = acc.group(1)

    # propertyType built as 'X bed ' + '' + 'flat'
    pt = re.search(r"var\s+propertyType\s*=\s*((?:'[^']*'\s*\+\s*)+'[^']*')", html)
    if pt:
        parts = re.findall(r"'([^']*)'", pt.group(1))
        joined = "".join(parts).strip()
        if joined:
            data["property_type_full"] = joined

    # category line contains '/SE10' as the postcode literal
    cat = re.search(r"\bcategory\s*:\s*([^\n]+)", html)
    if cat:
        m_pc = re.search(rf"'/({_UK_POSTCODE_AREA}(?:\s?\d[A-Z]{{2}})?)'", cat.group(1))
        if m_pc:
            data["postcode_area"] = m_pc.group(1)

    # share_title often ends with postcode in parens: "... (SE10)"
    if "postcode_area" not in data:
        share = re.search(r'share_title\s*:\s*"([^"]+)"', html)
        if share:
            m_pc = re.search(rf"\(({_UK_POSTCODE_AREA}(?:\s?\d[A-Z]{{2}})?)\)", share.group(1))
            if m_pc:
                data["postcode_area"] = m_pc.group(1)

    # brand: derive from featured / premium_ad / earlyBird js vars
    featured = re.search(r"var\s+featured\s*=\s*parseInt\(\s*'([^']*)'", html)
    premium = re.search(r"var\s+premium_ad\s*=\s*(true|false)", html)
    earlybird = re.search(r"var\s+earlyBird\s*=\s*\(\s*'([^']*)'\s*==\s*'Y'\s*\)", html)
    fnum = featured.group(1).strip() if featured else ""
    if fnum.isdigit() and int(fnum) > 0:
        data["brand"] = "featured"
    elif premium and premium.group(1) == "true":
        data["brand"] = "bold"
    elif earlybird and earlybird.group(1) == "Y":
        data["brand"] = "early_bird"
    else:
        data["brand"] = "free"

    # ad_status: <num> ? 'expired ...' : 'open'  → first operand 0 means 'open'
    status = re.search(r"ad_status\s*:\s*(\S+?)\s*\?\s*'([^']+)'\s*:\s*'([^']+)'", html)
    if status:
        first = status.group(1).strip().strip("\"'")
        data["ad_status"] = status.group(3) if first in ("0", "") else status.group(2)

    # fees apply: function returns 'y' if first operand truthy, else 'n'
    fees = re.search(r"function\s+feesApply\(\)\{\s*return\s+'([^']*)'", html)
    if fees:
        data["fees_apply"] = "y" if fees.group(1) else "n"

    # canonical lat/long from _sr.page.location
    loc = re.search(
        r'location\s*:\s*\{[^}]*latitude\s*:\s*"([0-9.\-]+)"[^}]*longitude\s*:\s*"([0-9.\-]+)"',
        html,
    )
    if loc:
        data["sr_latitude"] = loc.group(1)
        data["sr_longitude"] = loc.group(2)

    return data


def extract_postcode(soup, sr_data, title=None, location=None):
    """Return postcode area (e.g. 'SE10') from the most reliable source available."""
    if sr_data.get("postcode_area"):
        return sr_data["postcode_area"]

    for source in (title, location):
        if not source:
            continue
        m = re.search(rf"\b({_UK_POSTCODE_AREA}(?:\s?\d[A-Z]{{2}})?)\b", source)
        if m:
            return m.group(1)

    for tag in (soup.find("link", rel="canonical"),
                soup.find("meta", attrs={"property": "og:url"}),
                soup.find("meta", attrs={"property": "og:description"}),
                soup.find("meta", attrs={"name": "description"})):
        if not tag:
            continue
        text = tag.get("href") or tag.get("content") or ""
        m = re.search(rf"\b({_UK_POSTCODE_AREA}(?:\s?\d[A-Z]{{2}})?)\b", text)
        if m:
            return m.group(1)

    return None


def _detect_transport_type(li):
    for el in li.find_all(True):
        cls = " ".join(el.get("class") or []).lower()
        for t in _TRANSPORT_KEYWORDS:
            if t in cls:
                return t.replace("-", "_")
    img = li.find("img")
    if img and img.get("alt"):
        alt = img["alt"].lower()
        for t in ("underground", "tube", "overground", "national rail",
                  "rail", "dlr", "bus", "tram", "ferry"):
            if t in alt:
                return t.replace(" ", "_").replace("-", "_")
    return None


def extract_nearest_stations(soup, max_stations=4):
    """Extract up to `max_stations` nearby stations with name, distance, type, image."""
    stations = []
    seen = set()

    def add_station(name, distance, transport_type, image):
        if not name:
            return
        name = re.sub(r"\s+", " ", name).strip(" ,-:–—•·")
        # Strip a stray "Station" suffix only when bare
        if not name or len(name) > 80:
            return
        key = name.lower()
        if key in seen:
            return
        seen.add(key)
        stations.append({
            "name": name,
            "distance": distance,
            "transport_type": transport_type,
            "image": image,
        })

    # Strategy 1: dedicated stations/transport section by class
    section = None
    for sel in (
        "section.feature--stations", "section.feature--transport",
        "section.feature--travel", "section.feature--nearby",
        "div.nearest-stations", "ul.nearest-stations",
        "div.stations", "ul.stations",
    ):
        section = soup.select_one(sel)
        if section:
            break

    # Strategy 2: header text "nearest station(s)" / "transport links"
    if section is None:
        for h in soup.find_all(["h2", "h3", "h4"]):
            t = h.get_text(" ", strip=True).lower()
            if any(k in t for k in ("nearest station", "closest station",
                                     "transport link", "nearby station")):
                container = h.find_next(["ul", "ol", "dl", "div"])
                if container:
                    section = container
                    break

    items = []
    if section is not None:
        items = section.find_all("li")
        if not items:
            dts = section.find_all("dt")
            dds = section.find_all("dd")
            for dt, dd in zip(dts, dds):
                items.append(dt)
                items.append(dd)

    # Strategy 3: scan all <li>s for a station + distance combo
    if not items:
        for li in soup.find_all("li"):
            text = li.get_text(" ", strip=True)
            if not text:
                continue
            if (re.search(r"\b(station|tube|underground|rail|overground|dlr)\b", text, re.I)
                    and _DISTANCE_RE.search(text)):
                items.append(li)

    for li in items:
        text = li.get_text(" ", strip=True)
        if not text:
            continue

        dist_match = _DISTANCE_RE.search(text)
        distance = dist_match.group(0).strip() if dist_match else None
        name = text[: dist_match.start()] if dist_match else text

        img = li.find("img")
        station_image = None
        if img and img.get("src"):
            src = img["src"].strip()
            if src:
                station_image = src if src.startswith("http") else base_url + src

        transport_type = _detect_transport_type(li)
        add_station(name, distance, transport_type, station_image)
        if len(stations) >= max_stations:
            break

    return stations


def scrape_listing_advanced(url, paying, profile_flag=""):
    try:
        resp = SESSION.get(url, timeout=15)
        resp.raise_for_status()
        soup = BeautifulSoup(resp.text, "html.parser")
        html = resp.text
        if "The advertiser is not currently accepting applications" in html:
            print(f"🚫 Skipping {url} — advertiser not accepting applications.")
            return None

        # ✅ Canonical structured data from embedded _sr JS object
        sr_data = extract_sr_data(html)
        # ✅ Whole property detection + dedicated price extraction
        is_whole_property = detect_whole_property(soup)
        whole_property_price = extract_whole_property_price(soup) if is_whole_property else None
        # ✅ Multi-room support (up to 6 rooms in one ad)
        room_options = extract_room_options(soup, max_rooms=6)  # list of rooms
        room_deposits, generic_deposit = extract_room_deposits(soup)            # dict {room_index: deposit}

        # attach deposits to room options
        for r in room_options:
            idx = r.get("room_index")
            if idx in room_deposits:
                r["deposit"] = room_deposits[idx]
            else:
                r["deposit"] = None

        room_count = len(room_options) if room_options else 1

        room_prices = [r.get("price_pcm") for r in room_options if r.get("price_pcm") is not None]
        min_room_price_pcm = min(room_prices) if room_prices else None
        max_room_price_pcm = max(room_prices) if room_prices else None
        # ✅ Recommended: if multi-room, make "price" = min room price for sorting in Sheets
        # and avoid using a single "deposit" field (ambiguous). Deposits will live in room{i}_deposit.
        if room_count > 1 and min_room_price_pcm is not None:
            # override listing-level price later after it's parsed too
            pass

        # Flatten room columns: only for the number of rooms in this listing (deposits/type/price only for rooms that exist)
        flat_rooms = {}
        for i in range(1, room_count + 1):
            flat_rooms[f"room{i}_type"] = ""
            flat_rooms[f"room{i}_price_pcm"] = ""
            flat_rooms[f"room{i}_deposit"] = ""

        for r in room_options:
            i = r.get("room_index")
            if i and 1 <= i <= room_count:
                flat_rooms[f"room{i}_type"] = r.get("room_type") or ""
                flat_rooms[f"room{i}_price_pcm"] = r.get("price_pcm") or ""
                flat_rooms[f"room{i}_deposit"] = r.get("deposit") or ""
               
        

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


        # Latitude / Longitude — prefer canonical _sr.page.location, fall back to script regex
        latitude = sr_data.get("sr_latitude", "N/A")
        longitude = sr_data.get("sr_longitude", "N/A")
        if latitude == "N/A" or longitude == "N/A":
            for script in soup.find_all("script"):
                if not script.string:
                    continue
                m = re.search(
                    r'location["\s]*:["\s]*{[^}]*latitude["\s]*:["\s]*"?([0-9.-]+)"?[^}]*longitude["\s]*:["\s]*"?([0-9.-]+)"?',
                    script.string,
                )
                if m:
                    latitude, longitude = m.group(1), m.group(2)
                    break

        # Photo gallery (dedented out of the script loop where it was previously trapped)
        all_photo_urls = []
        photo_href_re = re.compile(
            r"^https://photos2\.spareroom\.co\.uk/images/flatshare/listings/large/[0-9]+/[0-9]+/[0-9]+\.jpg$"
        )
        for sel in ("dl.photo-gallery__main-image-wrapper",
                    "div.photo-gallery__thumbnails"):
            container = soup.select_one(sel)
            if not container:
                continue
            for link in container.find_all("a", href=photo_href_re):
                photo_url = link.get("href")
                if photo_url and photo_url not in all_photo_urls:
                    all_photo_urls.append(photo_url)

        first_photo_url = all_photo_urls[0] if all_photo_urls else None
        photo_count = len(all_photo_urls)
        all_photos = ", ".join(all_photo_urls) if all_photo_urls else None

        # 🚫 Skip listings with NO images
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
        # ✅ Always use whole-property header price if available
        if is_whole_property and whole_property_price is not None:
            price = int(whole_property_price)
        # ✅ If multi-room listing, use min room price as main price
        if room_count > 1 and min_room_price_pcm is not None:
            price = int(min_room_price_pcm)
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
                # Property type (reliable) + whole property override
        property_type, is_whole_property_from_kf = detect_property_type_from_key_features(soup)

        # If key-features says "to rent", treat as whole property
        if is_whole_property_from_kf:
            is_whole_property = True  # overrides earlier detection
            

        # ✅ Extract structured features
        features = extract_feature_list(soup)
        available_date = features.get("Available")
        min_term = features.get("Minimum term")
        max_term = features.get("Maximum term")
                # ✅ Deposit can be "Deposit" or "Security deposit"
        deposit = features.get("Deposit")
        if not deposit:
            deposit = features.get("Security deposit")

        # If we found a numeric generic deposit in Extra cost,
        # use it ONLY for single-room listings
        if room_count == 1 and generic_deposit is not None:
            deposit = generic_deposit

        # If multi-room, listing-level deposit is ambiguous → clear it
        if room_count > 1:
            deposit = None
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

        # ✅ Postcode (best source: _sr.ecommerce.category, falls back to title/location/canonical)
        postcode = extract_postcode(soup, sr_data, title=title, location=location)

        # ✅ Nearest stations (up to 4)
        nearby_stations = extract_nearest_stations(soup, max_stations=4)
        station_count = len(nearby_stations)
        flat_stations = {}
        for i in range(1, station_count + 1):
            flat_stations[f"station{i}_name"] = ""
            flat_stations[f"station{i}_distance"] = ""
            flat_stations[f"station{i}_type"] = ""
            flat_stations[f"station{i}_image"] = ""
        for i, st in enumerate(nearby_stations, start=1):
            flat_stations[f"station{i}_name"] = st.get("name") or ""
            flat_stations[f"station{i}_distance"] = st.get("distance") or ""
            flat_stations[f"station{i}_type"] = st.get("transport_type") or ""
            flat_stations[f"station{i}_image"] = st.get("image") or ""

        # ✅ Derived listing metadata from _sr
        ad_status = sr_data.get("ad_status") or "available"
        advertiser_type = sr_data.get("variant")          # 'agent' or 'private'
        ad_brand = sr_data.get("brand")                    # bold/free/featured/early_bird
        ad_verified = sr_data.get("ad_verified")           # yes/no
        ad_video = sr_data.get("ad_video")                 # yes/no
        ad_freshness = sr_data.get("dimension22")          # 'new today' / 'X days ago'
        fees_apply = sr_data.get("fees_apply")             # y/n
        property_type_full = sr_data.get("property_type_full")  # '3 bed flat'

        # Final result dictionary
        lat, lon = extract_coordinates(latitude, longitude)
        result = {
            "url": url,
            "title": title,
            "agent_name": agent_name,
            "location": location,
            "postcode": postcode,
            "latitude": lat,
            "longitude": lon,
            "status": ad_status,
            "price": price,
            "description": description,  # ✅ emojis + newlines preserved
            "property_type": property_type_full or property_type,
            "advertiser_type": advertiser_type,
            "ad_tier": ad_brand,
            "ad_verified": ad_verified,
            "ad_video": ad_video,
            "freshness": ad_freshness,
            "fees_apply": fees_apply,
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
            "flag_color": "",  # ✅ Will be populated from profile or manual flags
            "room_count": room_count,
            "min_room_price_pcm": min_room_price_pcm,
            "max_room_price_pcm": max_room_price_pcm,
            "station_count": station_count,
            **flat_rooms,  # room1_type, room1_price_pcm, room1_deposit ... only for rooms that exist
            **flat_stations,  # station1_name, station1_distance, station1_type, station1_image
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
    
    # 1️⃣ Read existing data with flags (handle duplicate headers in sheet)
    raw_headers = sheet.row_values(1)
    all_values = sheet.get_all_values()
    # Deduplicate headers so we can build dicts (first occurrence keeps name, rest get _2, _3, ...)
    seen = {}
    unique_headers = []
    for h in raw_headers:
        key = (h or "").strip() or f"_empty_{len(unique_headers)}"
        if key in seen:
            seen[key] += 1
            unique_headers.append(f"{key}_{seen[key]}")
        else:
            seen[key] = 0
            unique_headers.append(key)
    existing_headers = raw_headers  # keep original for later comparison
    existing_data = []
    for row_values in (all_values[1:] if len(all_values) > 1 else []):
        row_dict = {}
        for i, val in enumerate(row_values):
            if i < len(unique_headers):
                row_dict[unique_headers[i]] = val
        existing_data.append(row_dict)
    
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

    # 4b️⃣ Drop columns that are entirely null or empty (only show columns with at least one value)
    def _is_empty(val):
        if pd.isna(val):
            return True
        s = str(val).strip()
        return s == "" or s.lower() in ("nan", "none", "n/a")

    cols_to_drop = [c for c in df_output.columns if df_output[c].apply(_is_empty).all()]
    if cols_to_drop:
        df_output = df_output.drop(columns=cols_to_drop)
        print(f"\n📋 Dropped {len(cols_to_drop)} empty columns: {', '.join(cols_to_drop)}")

    # 5️⃣ Ensure headers match dataframe (strip any leading/trailing spaces)
    df_output.columns = [str(c).strip() for c in df_output.columns]
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
    rows = df_output.fillna("").astype(str).values.tolist()
    sheet.append_rows(rows, value_input_option="RAW")   

    print(f"\n✅ Successfully uploaded {len(df_output)} listings to Sheet with preserved flags!")


# Call main() to scrape each individual listing
if __name__ == "__main__":
    main(listings)