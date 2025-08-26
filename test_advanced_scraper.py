#!/usr/bin/env python3
"""
Test Advanced Scraper
Test the new advanced scraper on a few URLs to see improved data extraction.
"""

from scrape_and_process import scrape_listing_advanced
import json

def test_scraper():
    # Test URLs from the new_links_to_scrape.csv
    test_urls = [
        "https://www.spareroom.co.uk/flatshare/london/finsbury_park/17912019",
        "https://www.spareroom.co.uk/flatshare/london/wandsworth_road/17917106"
    ]
    
    print("Testing Advanced Property Scraper")
    print("=" * 60)
    
    for i, url in enumerate(test_urls, 1):
        print(f"\n[{i}/{len(test_urls)}] Testing: {url}")
        print("-" * 50)
        
        try:
            result = scrape_listing_advanced(url)
            if result:
                print(f"✅ Successfully scraped!")
                print(f"Title: {result.get('title', 'N/A')}")
                print(f"Location: {result.get('location', 'N/A')}")
                print(f"Price: {result.get('price', 'N/A')}")
                print(f"Property Type: {result.get('property_type', 'N/A')}")
                print(f"Photo Count: {result.get('photo_count', 'N/A')}")
                print(f"Coordinates: {result.get('latitude', 'N/A')}, {result.get('longitude', 'N/A')}")
                
                # Show additional features
                additional_features = {k: v for k, v in result.items() 
                                    if k not in ['url', 'title', 'location', 'latitude', 'longitude', 
                                               'price', 'description', 'property_type', 'available_date', 
                                               'photo_count', 'first_photo_url', 'all_photos', 
                                               'contact_info', 'management_company', 'amenities']}
                
                if additional_features:
                    print(f"\nAdditional Features Extracted:")
                    for key, value in additional_features.items():
                        if value and value != "N/A":
                            print(f"  {key}: {value}")
                
                print(f"\nTotal fields extracted: {len(result)}")
                
            else:
                print(f"❌ Failed to scrape")
                
        except Exception as e:
            print(f"❌ Error: {str(e)}")
        
        print()

if __name__ == "__main__":
    test_scraper()
