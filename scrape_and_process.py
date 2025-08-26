#!/usr/bin/env python3
"""
Advanced Property Scraping and Processing Pipeline
This script scrapes new property links with comprehensive data extraction.
"""

import csv
import requests
from bs4 import BeautifulSoup
import re
import time
import json

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
            title = title_elem.get_text(strip=True)
        
        # Try to extract price from title if it contains price info
        price_from_title = "N/A"
        price_patterns = [
            r'£(\d+(?:,\d+)?(?:\.\d{2})?)',
            r'(\d+(?:,\d+)?(?:\.\d{2})?)pcm',
            r'(\d+(?:,\d+)?(?:\.\d{2})?)PCM'
        ]
        for pattern in price_patterns:
            match = re.search(pattern, title, re.IGNORECASE)
            if match:
                price_from_title = f"£{match.group(1)}"
                break
        
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
        
        # Also look for images in other common locations
        additional_image_selectors = [
            "img[src*='spareroom']",
            "img[src*='photo']",
            "img[src*='image']",
            ".gallery img",
            ".photos img",
            ".images img"
        ]
        
        for selector in additional_image_selectors:
            try:
                elements = soup.select(selector)
                for elem in elements:
                    photo_url = elem.get('src', '')
                    if photo_url and photo_url not in all_photo_urls:
                        # Ensure it's a full URL
                        if photo_url.startswith('//'):
                            photo_url = 'https:' + photo_url
                        elif photo_url.startswith('/'):
                            photo_url = 'https://photos2.spareroom.co.uk' + photo_url
                        
                        if photo_url and photo_url not in all_photo_urls:
                            all_photo_urls.append(photo_url)
            except:
                continue
        
        # Set first photo URL and photo count
        first_photo_url = all_photo_urls[0] if all_photo_urls else "N/A"
        photo_count = str(len(all_photo_urls))
        
        # Join all photo URLs for storage
        all_photos = ", ".join(all_photo_urls) if all_photo_urls else "N/A"
        
        # Look for price in various elements
        price = price_from_title
        price_selectors = [
            ".price",
            ".rent",
            ".amount",
            "[class*='price']",
            "[class*='rent']",
            "[class*='amount']",
            "span:contains('£')",
            "div:contains('£')"
        ]
        
        for selector in price_selectors:
            try:
                elements = soup.select(selector)
                for elem in elements:
                    text = elem.get_text(strip=True)
                    if '£' in text and price == "N/A":
                        # Extract just the price part
                        price_match = re.search(r'£(\d+(?:,\d+)?(?:\.\d{2})?)', text)
                        if price_match:
                            price = f"£{match.group(1)}"
                            break
            except:
                continue
        
        # Extract description from various possible locations
        description = "N/A"
        
        # First, try to find the specific feature__description-body div
        feature_desc_body = soup.find("div", class_="feature__description-body")
        if feature_desc_body:
            description = feature_desc_body.get_text(strip=True)
            # Clean up the description by removing extra whitespace and newlines
            description = re.sub(r'\s+', ' ', description)
            # Truncate if too long
            if len(description) > 1000:
                description = description[:1000] + "..."
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
                            description = text[:300] + "..." if len(text) > 300 else text
                            break
                    if description != "N/A":
                        break
                except:
                    continue
        
        # Now try to extract price from description if we still don't have one
        if price == "N/A":
            # Look for price patterns in the description
            price_patterns = [
                r'£(\d+(?:,\d+)?(?:\.\d{2})?)pcm',
                r'£(\d+(?:,\d+)?(?:\.\d{2})?)PCM',
                r'£(\d+(?:,\d+)?(?:\.\d{2})?)\s*per\s*month',
                r'£(\d+(?:,\d+)?(?:\.\d{2})?)\s*monthly'
            ]
            
            search_text = (title + " " + description).lower()
            for pattern in price_patterns:
                match = re.search(pattern, search_text, re.IGNORECASE)
                if match:
                    price = f"£{match.group(1)}"
                    break
        
        # Extract property type from title or description
        property_type = "N/A"
        type_keywords = ["room", "bedroom", "studio", "flat", "apartment", "house", "en-suite", "ensuite"]
        search_text = (title + " " + description).lower()
        for keyword in type_keywords:
            if keyword in search_text:
                property_type = keyword.title()
                break
        
        # Extract available date
        available_date = "N/A"
        date_patterns = [
            r'available\s+(?:from\s+)?(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})',
            r'(\d{1,2}[/-]\d{1,2}[/-]\d{2,4})',
            r'(immediately|now|asap)',
            r'(january|february|march|april|may|june|july|august|september|october|november|december)\s+\d{4}'
        ]
        
        search_text = (title + " " + description).lower()
        for pattern in date_patterns:
            match = re.search(pattern, search_text, re.IGNORECASE)
            if match:
                available_date = match.group(1)
                break
        
        # Extract contact info
        contact_info = "N/A"
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
                        contact_info = text
                        break
                if contact_info != "N/A":
                    break
            except:
                continue
        
        # Extract property management company name
        management_company = "N/A"
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
                        management_company = text
                        break
                if management_company != "N/A":
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
                features["price_pcm"] = price_elem.get_text(strip=True)
            if room_elem:
                features["room_type"] = room_elem.get_text(strip=True)
        
        # Extract availability details
        availability_section = soup.find("section", class_="feature--availability")
        if availability_section:
            availability_items = availability_section.find_all("dt", class_="feature-list__key")
            availability_values = availability_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(availability_items):
                if i < len(availability_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_")
                    value = availability_values[i].get_text(strip=True)
                    features[key] = value
        
        # Extract extra costs
        extra_cost_section = soup.find("section", class_="feature--extra-cost")
        if extra_cost_section:
            cost_items = extra_cost_section.find_all("dt", class_="feature-list__key")
            cost_values = extra_cost_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(cost_items):
                if i < len(cost_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_").replace("?", "")
                    value = cost_values[i].get_text(strip=True)
                    features[key] = value
        
        # Extract amenities
        amenities_section = soup.find("section", class_="feature--amenities")
        if amenities_section:
            amenity_items = amenities_section.find_all("dt", class_="feature-list__key")
            amenity_values = amenities_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(amenity_items):
                if i < len(amenity_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_").replace("/", "_")
                    value = amenity_values[i].get_text(strip=True)
                    features[key] = value
        
        # Extract current household details
        household_section = soup.find("section", class_="feature--current-household")
        if household_section:
            household_items = household_section.find_all("dt", class_="feature-list__key")
            household_values = household_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(household_items):
                if i < len(household_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_").replace("#", "number").replace("?", "")
                    value = household_values[i].get_text(strip=True)
                    features[key] = value
        
        # Extract household preferences
        preferences_section = soup.find("section", class_="feature--household-preferences")
        if preferences_section:
            preference_items = preferences_section.find_all("dt", class_="feature-list__key")
            preference_values = preferences_section.find_all("dd", class_="feature-list__value")
            
            for i, key_elem in enumerate(preference_items):
                if i < len(preference_values):
                    key = key_elem.get_text(strip=True).lower().replace(" ", "_").replace("?", "").replace("ok", "allowed")
                    value = preference_values[i].get_text(strip=True)
                    features[key] = value
        
        # Extract any other useful information
        amenities = []
        amenity_keywords = ["wifi", "internet", "parking", "garden", "balcony", "ensuite", "bills included", "furnished"]
        search_text = (title + " " + description).lower()
        for keyword in amenity_keywords:
            if keyword in search_text:
                amenities.append(keyword.title())
        
        # Create result dictionary with all features
        result = {
            "url": url,
            "title": title,
            "location": location_from_url,
            "latitude": latitude,
            "longitude": longitude,
            "price": price,
            "description": description,
            "property_type": property_type,
            "available_date": available_date,
            "photo_count": photo_count,
            "first_photo_url": first_photo_url,
            "contact_info": contact_info,
            "management_company": management_company,
            "amenities": ", ".join(amenities) if amenities else "N/A",
            "all_photos": all_photos # Add all photo URLs to the result
        }
        
        # Add all extracted features to the result
        for key, value in features.items():
            result[key] = value
        
        # If we still don't have a price, try to use price_pcm as fallback
        if result.get("price") == "N/A" and result.get("price_pcm"):
            # Extract just the numeric part from price_pcm (e.g., "£825 pcm" -> "£825")
            price_pcm = result["price_pcm"]
            price_match = re.search(r'£(\d+(?:,\d+)?(?:\.\d{2})?)', price_pcm)
            if price_match:
                result["price"] = f"£{price_match.group(1)}"
        
        return result
    except Exception as e:
        print(f"Failed to scrape {url}: {e}")
        return None

def main():
    results = []
    all_keys = set()  # To collect all possible keys
    
    # Read URLs from the input file
    input_file = "new_links_to_scrape.csv"
    
    print(f"Advanced Property Scraping Pipeline")
    print("=" * 60)
    print(f"Reading URLs from: {input_file}")
    
    # Read URLs from input file
    urls = []
    with open(input_file, 'r', encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if line.startswith('http') and not line.startswith('#'):
                urls.append(line)
    
    print(f"Found {len(urls)} URLs to scrape")
    print("=" * 60)
    
    # Scrape each URL
    for i, url in enumerate(urls, 1):
        print(f"\n[{i}/{len(urls)}] Scraping: {url}")
        data = scrape_listing_advanced(url)
        if data:
            results.append(data)
            # Collect all keys found
            all_keys.update(data.keys())
            print(f"   Successfully scraped: {data['title'][:50]}...")
        else:
            print(f"   Failed to scrape")
        
        # Add a small delay to be respectful to the server
        if i < len(urls):
            time.sleep(2)
    
    # Define the order of standard columns
    standard_keys = ["url", "title", "location", "latitude", "longitude", "price", "description", 
                    "property_type", "available_date", "photo_count", "first_photo_url", "all_photos",
                    "contact_info", "management_company", "amenities"]
    
    # Add any additional keys found in the data
    additional_keys = sorted(list(all_keys - set(standard_keys)))
    keys = standard_keys + additional_keys
    
    # Save results with all extracted features
    output_file = "new_properties_scraped.csv"
    with open(output_file, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=keys)
        writer.writeheader()
        writer.writerows(results)
    
    print(f"\n" + "=" * 60)
    print(f"Advanced scraping complete!")
    print(f"Results saved to: {output_file}")
    print(f"Total properties scraped: {len(results)}")
    print(f"Total columns: {len(keys)}")
    print(f"Standard columns: {len(standard_keys)}")
    print(f"Additional feature columns: {len(additional_keys)}")
    
    if additional_keys:
        print(f"\nAdditional features extracted:")
        for key in additional_keys:
            print(f"   - {key}")
    
    print(f"\nNext steps:")
    print(f"   1. Review the scraped data in {output_file}")
    print(f"   2. Clean and validate the data if needed")
    print(f"   3. Import into your Laravel database")

if __name__ == "__main__":
    main()
