#!/usr/bin/env python3
"""
Property Link Duplicate Checker and Manager
This script helps you check for duplicates and add new property links to your CSV files.
"""

import csv
import os
from urllib.parse import urlparse, parse_qs
import sys

def clean_url(url):
    """Clean URL by removing UTM parameters and other tracking parameters"""
    if not url or not url.startswith('http'):
        return url
    
    try:
        parsed = urlparse(url)
        # Remove UTM and other tracking parameters
        query_params = parse_qs(parsed.query)
        clean_params = {k: v for k, v in query_params.items() 
                       if not k.startswith('utm_') and k not in ['utm_source', 'utm_medium', 'utm_campaign']}
        
        # Rebuild URL without tracking parameters
        if clean_params:
            new_query = '&'.join([f"{k}={v[0]}" for k, v in clean_params.items()])
            clean_url = f"{parsed.scheme}://{parsed.netloc}{parsed.path}?{new_query}"
        else:
            clean_url = f"{parsed.scheme}://{parsed.netloc}{parsed.path}"
        
        return clean_url
    except:
        return url

def extract_property_id(url):
    """Extract property ID from Spareroom URL"""
    if 'spareroom.co.uk' in url:
        # Look for patterns like /17913185 or ?id=17913185
        if '/flatshare/' in url:
            parts = url.split('/')
            for part in parts:
                if part.isdigit() and len(part) >= 7:
                    return part
    return None

def load_existing_urls(csv_file):
    """Load existing URLs from CSV file"""
    existing_urls = set()
    existing_ids = set()
    
    if not os.path.exists(csv_file):
        print(f"Warning: {csv_file} not found")
        return existing_urls, existing_ids
    
    try:
        with open(csv_file, 'r', encoding='utf-8') as f:
            reader = csv.DictReader(f)
            for row in reader:
                if 'url' in row and row['url'] and not row['url'].startswith('#'):
                    clean_url_str = clean_url(row['url'])
                    existing_urls.add(clean_url_str)
                    
                    # Also track property IDs
                    prop_id = extract_property_id(row['url'])
                    if prop_id:
                        existing_ids.add(prop_id)
    
    except Exception as e:
        print(f"Error reading {csv_file}: {e}")
    
    return existing_urls, existing_ids

def check_duplicates(new_links, existing_urls, existing_ids):
    """Check for duplicates in new links"""
    duplicates = []
    new_unique = []
    
    for link in new_links:
        clean_link = clean_url(link)
        prop_id = extract_property_id(link)
        
        if clean_link in existing_urls:
            duplicates.append(('URL', link))
        elif prop_id and prop_id in existing_ids:
            duplicates.append(('ID', link))
        else:
            new_unique.append(link)
    
    return duplicates, new_unique

def add_new_links_to_csv(new_links, csv_file, template_row=None):
    """Add new links to CSV file"""
    if not new_links:
        print("No new links to add")
        return
    
    # Read existing CSV to get headers and sample data
    headers = []
    sample_data = {}
    
    if os.path.exists(csv_file):
        with open(csv_file, 'r', encoding='utf-8') as f:
            reader = csv.DictReader(f)
            headers = reader.fieldnames
            # Get first row as template
            try:
                sample_data = next(reader)
            except StopIteration:
                pass
    
    # Create template row for new entries
    if not template_row:
        template_row = {
            'url': '',
            'title': 'New Property - To Be Scraped',
            'location': 'London',
            'latitude': '',
            'longitude': '',
            'price': '',
            'description': 'Property details to be scraped',
            'property_type': 'Room',
            'available_date': 'now',
            'photo_count': '0',
            'first_photo_url': '',
            'all_photos': '',
            'contact_info': 'Free tocontact',
            'management_company': 'N/A',
            'amenities': 'To be determined',
            'age': '',
            'ages': '',
            'any_pets': 'No',
            'available': 'now',
            'balcony_roof_terrace': 'No',
            'bills_included': 'Yes',
            'broadband_included': 'Yes',
            'couples_allowed': 'Yes',
            'deposit': '',
            'deposit_room_1': '',
            'deposit_room_2': '',
            'deposit_room_3': '',
            'deposit_room_4': '',
            'disabled_access': 'No',
            'furnishings': 'Furnished',
            'garage': 'No',
            'garden_patio': 'No',
            'gender': '',
            'living_room': 'shared',
            'max_age': '',
            'maximum_term': '',
            'min_age': '',
            'minimum_term': '',
            'number_housemates': '',
            'number_flatmates': '',
            'occupation': '',
            'parking': 'No',
            'pets_allowed': 'No',
            'price_pcm': '',
            'references': '',
            'room_type': 'double',
            'smalloweding_allowed': 'No',
            'smoker': 'No',
            'total_number_rooms': '',
            'vegetarian_vegan': ''
        }
    
    # Ensure all headers are present in template
    for header in headers:
        if header not in template_row:
            template_row[header] = ''
    
    # Add new links to CSV
    with open(csv_file, 'a', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=headers)
        
        for link in new_links:
            new_row = template_row.copy()
            new_row['url'] = link
            writer.writerow(new_row)
    
    print(f"Added {len(new_links)} new links to {csv_file}")

def main():
    print("🏠 Property Link Duplicate Checker and Manager")
    print("=" * 50)
    
    # Check existing files
    detailed_csv = "results_detailed.csv"
    cleaned_csv = "results_cleaned.csv"
    new_links_csv = "new_links_to_scrape.csv"
    
    print(f"📁 Checking existing CSV files...")
    existing_urls_detailed, existing_ids_detailed = load_existing_urls(detailed_csv)
    existing_urls_cleaned, existing_ids_cleaned = load_existing_urls(cleaned_csv)
    existing_urls_new, existing_ids_new = load_existing_urls(new_links_csv)
    
    print(f"📊 Found {len(existing_urls_detailed)} URLs in {detailed_csv}")
    print(f"📊 Found {len(existing_urls_cleaned)} URLs in {cleaned_csv}")
    print(f"📊 Found {len(existing_urls_new)} URLs in {new_links_csv}")
    
    # Combine all existing URLs and IDs
    all_existing_urls = existing_urls_detailed.union(existing_urls_cleaned).union(existing_urls_new)
    all_existing_ids = existing_ids_detailed.union(existing_ids_cleaned).union(existing_ids_new)
    
    print(f"🔍 Total unique URLs: {len(all_existing_urls)}")
    print(f"🔍 Total unique property IDs: {len(all_existing_ids)}")
    
    # Get new links from user
    print("\n📝 Enter new property links (one per line, press Enter twice when done):")
    new_links = []
    
    while True:
        link = input().strip()
        if not link:
            if new_links:
                break
            else:
                print("Please enter at least one link")
                continue
        
        if link.startswith('http'):
            new_links.append(link)
        else:
            print(f"⚠️  Skipping invalid URL: {link}")
    
    if not new_links:
        print("No links provided. Exiting.")
        return
    
    print(f"\n🔍 Checking {len(new_links)} new links for duplicates...")
    
    # Check for duplicates
    duplicates, new_unique = check_duplicates(new_links, all_existing_urls, all_existing_ids)
    
    # Report results
    if duplicates:
        print(f"\n⚠️  Found {len(duplicates)} duplicates:")
        for dup_type, link in duplicates:
            print(f"   {dup_type}: {link}")
    
    if new_unique:
        print(f"\n✅ Found {len(new_unique)} new unique links:")
        for link in new_unique:
            print(f"   {link}")
        
        # Ask user where to add new links
        print(f"\n📁 Where would you like to add the new links?")
        print("1. new_links_to_scrape.csv (RECOMMENDED - for scraping new properties)")
        print("2. results_cleaned.csv (main working file - for immediate import)")
        print("3. results_detailed.csv")
        print("4. Create new CSV file")
        
        choice = input("Enter choice (1-4): ").strip()
        
        if choice == "1":
            add_new_links_to_csv(new_unique, new_links_csv)
            print(f"\n🎉 Successfully added {len(new_unique)} new links to {new_links_csv}!")
            print(f"💡 Next steps:")
            print(f"   1. Run your scraping script on {new_links_csv}")
            print(f"   2. After scraping, merge results with your existing data")
            print(f"   3. Import final results into Laravel")
        elif choice == "2":
            add_new_links_to_csv(new_unique, cleaned_csv)
            print(f"\n🎉 Successfully added {len(new_unique)} new links to {cleaned_csv}!")
            print(f"💡 Next steps:")
            print(f"   1. Run your scraping script on the new links")
            print(f"   2. Use Laravel command: php artisan properties:import {cleaned_csv}")
        elif choice == "3":
            add_new_links_to_csv(new_unique, detailed_csv)
            print(f"\n🎉 Successfully added {len(new_unique)} new links to {detailed_csv}!")
        elif choice == "4":
            new_filename = input("Enter new CSV filename: ").strip()
            if not new_filename.endswith('.csv'):
                new_filename += '.csv'
            add_new_links_to_csv(new_unique, new_filename)
            print(f"\n🎉 Successfully added {len(new_unique)} new links to {new_filename}!")
        else:
            print("Invalid choice. Exiting.")
            return
    
    else:
        print("\n❌ No new unique links found. All links are duplicates.")

if __name__ == "__main__":
    main()
