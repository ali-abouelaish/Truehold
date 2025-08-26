import csv
import requests
from bs4 import BeautifulSoup
import re
import time
import json

def clean_text(text):
    """Clean and normalize text data"""
    if not text or text == "N/A":
        return None
    # Remove extra whitespace and newlines
    text = re.sub(r'\s+', ' ', text.strip())
    # Remove HTML tags if any
    text = re.sub(r'<[^>]+>', '', text)
    # Truncate if too long
    if len(text) > 500:
        text = text[:500] + "..."
    return text

def extract_price(price_text):
    """Extract and normalize price information"""
    if not price_text or price_text == "N/A":
        return None
    
    # Remove £ symbol and extract numbers
    price_match = re.search(r'£?(\d+(?:,\d+)?(?:\.\d{2})?)', str(price_text))
    if price_match:
        # Remove commas and convert to integer
        price = price_match.group(1).replace(',', '')
        try:
            return int(float(price))
        except:
            return None
    return None

def extract_coordinates(lat_text, lon_text):
    """Extract and validate coordinates"""
    try:
        lat = float(lat_text) if lat_text and lat_text != "N/A" else None
        lon = float(lon_text) if lon_text and lon_text != "N/A" else None
        
        # Validate coordinate ranges
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

def scrape_listing_advanced(url):
    try:
        # Add headers to mimic a real browser
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'en-US,en;q=0.5',
            'Accept-Encoding': 'gzip, deflate',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1',
        }
        
        resp = requests.get(url, headers=headers)
        resp.raise_for_status()
        soup = BeautifulSoup(resp.text, "html.parser")
        
        # Extract title
        title = "N/A"
        title_elem = soup.find("h1") or soup.find("h2") or soup.find("title")
        if title_elem:
            title = clean_text(title_elem.get_text())
        
        # Extract location from URL
        location_from_url = "N/A"
        url_match = re.search(r'/london/([^/]+)/', url)
        if url_match:
            location_from_url = url_match.group(1).replace('_', ' ').title()
        
        # Extract longitude and latitude from JavaScript data
        latitude = "N/A"
        longitude = "N/A"
        
        # Look for location data in script tags
        script_tags = soup.find_all("script")
        for script in script_tags:
            if script.string:
                script_content = script.string
                
                # Look for latitude and longitude patterns
                lat_match = re.search(r'latitude["\s]*:["\s]*"?([0-9.-]+)"?', script_content)
                lon_match = re.search(r'longitude["\s]*:["\s]*"?([0-9.-]+)"?', script_content)
                
                if lat_match:
                    latitude = lat_match.group(1)
                if lon_match:
                    longitude = lon_match.group(1)
                
                # Also look for location object pattern
                location_match = re.search(r'location["\s]*:["\s]*{[^}]*latitude["\s]*:["\s]*"?([0-9.-]+)"?[^}]*longitude["\s]*:["\s]*"?([0-9.-]+)"?', script_content)
                if location_match:
                    latitude = location_match.group(1)
                    longitude = location_match.group(2)
        
        # Extract all photo URLs
        all_photo_urls = []
        photo_elements = soup.find_all("img", src=re.compile(r"photos2\.spareroom\.co\.uk"))
        if photo_elements:
            for photo_elem in photo_elements:
                photo_url = photo_elem.get('src', '')
                # Ensure it's a full URL
                if photo_url.startswith('//'):
                    photo_url = 'https:' + photo_url
                elif photo_url.startswith('/'):
                    photo_url = 'https://photos2.spareroom.co.uk' + photo_url
                
                if photo_url and photo_url not in all_photo_urls:
                    all_photo_urls.append(photo_url)
        
        # Set first photo URL and photo count
        first_photo_url = all_photo_urls[0] if all_photo_urls else None
        photo_count = len(all_photo_urls)
        
        # Join all photo URLs for storage
        all_photos = ", ".join(all_photo_urls) if all_photo_urls else None
        
        # Look for price in various elements
        price = None
        price_selectors = [
            ".price",
            ".rent",
            ".amount",
            "[class*='price']",
            "[class*='rent']",
            "[class*='amount']"
        ]
        
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
        
        # Extract description from various possible locations
        description = None
        
        # First, try to find the specific feature__description-body div
        feature_desc_body = soup.find("div", class_="feature__description-body")
        if feature_desc_body:
            description = clean_text(feature_desc_body.get_text())
        else:
            # Fallback to other selectors if the specific element is not found
            desc_selectors = [
                ".description",
                ".details",
                ".content",
                "[class*='description']",
                "[class*='details']",
                "[class*='content']",
                "p",
                ".listing-details"
            ]
            
            for selector in desc_selectors:
                try:
                    elements = soup.select(selector)
                    for elem in elements:
                        text = elem.get_text(strip=True)
                        if len(text) > 50 and len(text) < 1000:  # Reasonable description length
                            description = clean_text(text)
                            break
                    if description:
                        break
                except:
                    continue
        
        # Extract property type from title or description
        property_type = None
        type_keywords = ["room", "bedroom", "studio", "flat", "apartment", "house", "en-suite", "ensuite"]
        search_text = (str(title) + " " + str(description)).lower()
        for keyword in type_keywords:
            if keyword in search_text:
                property_type = keyword.title()
                break
        
        # Extract available date
        available_date = None
        date_patterns = [
            r'available\s+(?:from\s+)?(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})',
            r'(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})',
            r'(immediately|now|asap)',
            r'(january|february|march|april|may|june|july|august|september|october|november|december)\s+\d{4}'
        ]
        
        search_text = (str(title) + " " + str(description)).lower()
        for pattern in date_patterns:
            match = re.search(pattern, search_text, re.IGNORECASE)
            if match:
                available_date = match.group(1)
                break
        
        # Extract contact info
        contact_info = None
        contact_selectors = [
            ".contact",
            ".agent",
            ".phone",
            ".email",
            "[class*='contact']",
            "[class*='agent']"
        ]
        
        for selector in contact_selectors:
            try:
                elements = soup.select(selector)
                for elem in elements:
                    text = elem.get_text(strip=True)
                    if len(text) > 5 and len(text) < 100:
                        contact_info = clean_text(text)
                        break
                if contact_info:
                    break
            except:
                continue
        
        # Extract property management company name
        management_company = None
        company_selectors = [
            ".profile-photo__name",
            "[class*='profile-photo'] [class*='name']",
            ".agent-name",
            ".company-name",
            ".management-company",
            "[class*='agent'] [class*='name']",
            "[class*='company'] [class*='name']"
        ]
        
        for selector in company_selectors:
            try:
                elements = soup.select(selector)
                for elem in elements:
                    text = elem.get_text(strip=True)
                    if len(text) > 2 and len(text) < 100 and text != "N/A":
                        management_company = clean_text(text)
                        break
                if management_company:
                    break
            except:
                continue
        
        # Extract detailed property features from structured sections
        features = {}
        
        # Extract price and room type
        price_room_section = soup.find("section", class_="feature--price_room_only")
        if price_room_section:
            price_elem = price_room_section.find("dt", class_="feature-list__key")
            room_elem = price_room_section.find("dd", class_="feature-list__value")
            if price_elem:
                features["price_pcm"] = clean_text(price_elem.get_text())
            if room_elem:
                features["room_type"] = clean_text(room_elem.get_text())
        
        # Extract availability details
        availability_section = soup.find("section", class_="feature--availability")
        if availability_section:
            availability_items = availability_section.find_all("dt", class_="feature-list__key")
            availability_values = availability_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(availability_items):
                if i < len(availability_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_")
                    value = clean_text(availability_values[i].get_text())
                    features[key] = value
        
        # Extract extra costs
        extra_cost_section = soup.find("section", class_="feature--extra-cost")
        if extra_cost_section:
            cost_items = extra_cost_section.find_all("dt", class_="feature-list__key")
            cost_values = extra_cost_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(cost_items):
                if i < len(cost_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_").replace("?", "")
                    value = clean_text(cost_values[i].get_text())
                    features[key] = value
        
        # Extract amenities
        amenities_section = soup.find("section", class_="feature--amenities")
        if amenities_section:
            amenity_items = amenities_section.find_all("dt", class_="feature-list__key")
            amenity_values = amenities_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(amenity_items):
                if i < len(amenity_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_").replace("/", "_")
                    value = clean_text(amenity_values[i].get_text())
                    features[key] = value
        
        # Extract current household details
        household_section = soup.find("section", class_="feature--current-household")
        if household_section:
            household_items = household_section.find_all("dt", class_="feature-list__key")
            household_values = household_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(household_items):
                if i < len(household_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_").replace("#", "number").replace("?", "")
                    value = clean_text(household_values[i].get_text())
                    features[key] = value
        
        # Extract household preferences
        preferences_section = soup.find("section", class_="feature--household-preferences")
        if preferences_section:
            preference_items = preferences_section.find_all("dt", class_="feature-list__key")
            preference_values = preferences_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(preference_items):
                if i < len(preference_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_").replace("?", "").replace("ok", "allowed")
                    value = clean_text(preference_values[i].get_text())
                    features[key] = value
        
        # Extract any other useful information
        amenities = []
        amenity_keywords = ["wifi", "internet", "parking", "garden", "balcony", "ensuite", "bills included", "furnished"]
        search_text = (str(title) + " " + str(description)).lower()
        for keyword in amenity_keywords:
            if keyword in search_text:
                amenities.append(keyword.title())
        
        # Create result dictionary with all features - aligned with database schema
        result = {
            "url": url,
            "title": title,
            "location": location_from_url,
            "latitude": None,  # Will be set below
            "longitude": None,  # Will be set below
            "status": "available",  # Default status
            "price": price,
            "description": description,
            "property_type": property_type,
            "available_date": available_date,
            "photo_count": photo_count,
            "first_photo_url": first_photo_url,
            "all_photos": all_photos,
            "photos": json.dumps(all_photo_urls) if all_photo_urls else None,  # JSON format for database
            "contact_info": contact_info,
            "management_company": management_company,
            "amenities": ", ".join(amenities) if amenities else None,
            "age": None,
            "ages": None,
            "any_pets": None,
            "available": available_date,
            "balcony_roof_terrace": None,
            "bills_included": None,
            "broadband_included": None,
            "couples_allowed": None,
            "deposit": None,
            "deposit_room_1": None,
            "deposit_room_2": None,
            "deposit_room_3": None,
            "deposit_room_4": None,
            "disabled_access": None,
            "furnishings": None,
            "garage": None,
            "garden_patio": None,
            "gender": None,
            "living_room": None,
            "max_age": None,
            "maximum_term": None,
            "min_age": None,
            "number_housemates": None,
            "number_flatmates": None,
            "occupation": None,
            "pets_allowed": None,
            "references": None,
            "room_type": None,
            "smalloweding_allowed": None,
            "total_number_rooms": None,
            "vegetarian_vegan": None,
            "minimum_term": None,
            "number": None
        }
        
        # Map specific features to database fields - only add if field exists in result
        feature_mapping = {
            "bills_included": "bills_included",
            "furnishings": "furnishings", 
            "room_type": "room_type",
            "minimum_term": "minimum_term",
            "maximum_term": "maximum_term",
            "deposit": "deposit",
            "couples_allowed": "couples_allowed",
            "pets_allowed": "pets_allowed",
            "disabled_access": "disabled_access",
            "garden_patio": "garden_patio",
            "balcony_roof_terrace": "balcony_roof_terrace",
            "garage": "garage",
            "living_room": "living_room",
            "gender": "gender",
            "ages": "ages",
            "number_housemates": "number_housemates",
            "number_flatmates": "number_flatmates",
            "total_number_rooms": "total_number_rooms",
            "age": "age",
            "any_pets": "any_pets",
            "broadband_included": "broadband_included",
            "deposit_room_1": "deposit_room_1",
            "deposit_room_2": "deposit_room_2", 
            "deposit_room_3": "deposit_room_3",
            "deposit_room_4": "deposit_room_4",
            "max_age": "max_age",
            "min_age": "min_age",
            "occupation": "occupation",
            "references": "references",
            "smalloweding_allowed": "smalloweding_allowed",
            "vegetarian_vegan": "vegetarian_vegan"
        }
        
        # Apply the mapping for any additional features - only if they exist in result
        for feature_key, db_field in feature_mapping.items():
            if feature_key in features and db_field in result:
                result[db_field] = features[feature_key]
        
        # Set coordinates with validation
        lat, lon = extract_coordinates(latitude, longitude)
        result["latitude"] = lat
        result["longitude"] = lon
        
        return result
    except Exception as e:
        print(f"Failed to scrape {url}: {e}")
        return None

def main():
    results = []
    
    # Read from legends.csv
    with open("legends.csv", newline="", encoding="utf-8") as f:
        reader = csv.reader(f)
        for row in reader:
            url = row[0].strip()
            # Skip empty rows
            if not url:
                continue
            print(f"Scraping {url}")
            data = scrape_listing_advanced(url)
            if data:
                results.append(data)
            # Add a small delay to be respectful to the server
            time.sleep(1)
    
    # Define the order of columns matching the database schema
    database_columns = [
        "url", "title", "location", "latitude", "longitude", "status", "price", 
        "description", "property_type", "available_date", "photo_count", 
        "first_photo_url", "all_photos", "photos", "contact_info", 
        "management_company", "amenities", "age", "ages", "any_pets", 
        "available", "balcony_roof_terrace", "bills_included", "broadband_included", 
        "couples_allowed", "deposit", "deposit_room_1", "deposit_room_2", 
        "deposit_room_3", "deposit_room_4", "disabled_access", "furnishings", 
        "garage", "garden_patio", "gender", "living_room", "max_age", 
        "maximum_term", "min_age", "number_housemates", "number_flatmates", 
        "occupation", "pets_allowed", "references", "room_type", 
        "smalloweding_allowed", "total_number_rooms", "vegetarian_vegan", 
        "minimum_term", "number"
    ]
    
    # Save results with database-aligned columns
    output_filename = "legends_scraped_results_improved.csv"
    with open(output_filename, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=database_columns)
        writer.writeheader()
        writer.writerows(results)
    
    print(f"Improved scraping complete. Results saved to {output_filename}")
    print(f"Successfully scraped {len(results)} properties from legends.csv")
    print(f"Data now aligned with database schema ({len(database_columns)} columns)")

if __name__ == "__main__":
    main()
