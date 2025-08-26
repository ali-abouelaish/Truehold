#!/usr/bin/env python3
"""
Quick Duplicate Checker for Property Links
Usage: python quick_check.py [csv_file] [link1] [link2] ...
"""

import csv
import os
import sys
from urllib.parse import urlparse, parse_qs

def clean_url(url):
    """Clean URL by removing UTM parameters"""
    if not url or not url.startswith('http'):
        return url
    
    try:
        parsed = urlparse(url)
        query_params = parse_qs(parsed.query)
        clean_params = {k: v for k, v in query_params.items() 
                       if not k.startswith('utm_')}
        
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
    if 'spareroom.co.uk' in url and '/flatshare/' in url:
        parts = url.split('/')
        for part in parts:
            if part.isdigit() and len(part) >= 7:
                return part
    return None

def check_duplicates_in_csv(csv_file, new_links):
    """Check if new links are duplicates in existing CSV"""
    if not os.path.exists(csv_file):
        print(f"CSV file not found: {csv_file}")
        return [], []
    
    existing_urls = set()
    existing_ids = set()
    
    # Load existing URLs
    with open(csv_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            if 'url' in row and row['url']:
                clean_url_str = clean_url(row['url'])
                existing_urls.add(clean_url_str)
                
                prop_id = extract_property_id(row['url'])
                if prop_id:
                    existing_ids.add(prop_id)
    
    # Check new links
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

def main():
    if len(sys.argv) < 3:
        print("Usage: python quick_check.py [csv_file] [link1] [link2] ...")
        print("Example: python quick_check.py results_cleaned.csv https://... https://...")
        return
    
    csv_file = sys.argv[1]
    new_links = sys.argv[2:]
    
    print(f"🔍 Checking {len(new_links)} links against {csv_file}")
    print("=" * 50)
    
    duplicates, new_unique = check_duplicates_in_csv(csv_file, new_links)
    
    if duplicates:
        print(f"\n⚠️  Found {len(duplicates)} duplicates:")
        for dup_type, link in duplicates:
            print(f"   {dup_type}: {link}")
    
    if new_unique:
        print(f"\n✅ Found {len(new_unique)} new unique links:")
        for link in new_unique:
            print(f"   {link}")
        
        print(f"\n💡 To add these to {csv_file}, you can:")
        print(f"   1. Use the interactive script: python check_duplicates.py")
        print(f"   2. Manually add them to the CSV file")
        print(f"   3. Use your scraping script to process them")
    
    else:
        print("\n❌ All links are duplicates!")

if __name__ == "__main__":
    main()
