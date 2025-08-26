#!/usr/bin/env python3
"""
Check all URLs in new_links_to_scrape.csv for duplicates
"""

import csv
import os
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
                    
                    prop_id = extract_property_id(row['url'])
                    if prop_id:
                        existing_ids.add(prop_id)
    
    except Exception as e:
        print(f"Error reading {csv_file}: {e}")
    
    return existing_urls, existing_ids

def check_all_new_links():
    """Check all URLs in new_links_to_scrape.csv for duplicates"""
    new_links_file = "new_links_to_scrape.csv"
    existing_files = ["results_cleaned.csv", "results_detailed.csv"]
    
    print("🏠 Checking all new links for duplicates")
    print("=" * 50)
    
    # Load all existing URLs
    all_existing_urls = set()
    all_existing_ids = set()
    
    for csv_file in existing_files:
        print(f"📁 Loading {csv_file}...")
        urls, ids = load_existing_urls(csv_file)
        all_existing_urls.update(urls)
        all_existing_ids.update(ids)
        print(f"   Found {len(urls)} URLs, {len(ids)} property IDs")
    
    print(f"\n🔍 Total existing: {len(all_existing_urls)} URLs, {len(all_existing_ids)} property IDs")
    
    # Load new links
    if not os.path.exists(new_links_file):
        print(f"❌ {new_links_file} not found!")
        return
    
    new_links = []
    with open(new_links_file, 'r', encoding='utf-8') as f:
        for line_num, line in enumerate(f, 1):
            line = line.strip()
            if line and line.startswith('http') and not line.startswith('#'):
                new_links.append((line_num, line))
    
    print(f"\n📝 Found {len(new_links)} new links to check")
    
    # Check for duplicates
    duplicates = []
    new_unique = []
    
    for line_num, link in new_links:
        clean_link = clean_url(link)
        prop_id = extract_property_id(link)
        
        if clean_link in all_existing_urls:
            duplicates.append(('URL', line_num, link))
        elif prop_id and prop_id in all_existing_ids:
            duplicates.append(('ID', line_num, link))
        else:
            new_unique.append((line_num, link))
    
    # Report results
    if duplicates:
        print(f"\n⚠️  Found {len(duplicates)} duplicates:")
        for dup_type, line_num, link in duplicates:
            print(f"   Line {line_num}: {dup_type} - {link}")
    
    if new_unique:
        print(f"\n✅ Found {len(new_unique)} new unique links:")
        for line_num, link in new_unique:
            print(f"   Line {line_num}: {link}")
        
        print(f"\n💡 Summary:")
        print(f"   Total new links: {len(new_links)}")
        print(f"   Duplicates found: {len(duplicates)}")
        print(f"   Unique new links: {len(new_unique)}")
        
        # Ask if user wants to clean up duplicates
        print(f"\n🧹 Would you like to remove duplicate lines from {new_links_file}?")
        print("   This will keep only the unique links for scraping.")
        
        choice = input("   Remove duplicates? (y/n): ").strip().lower()
        if choice == 'y':
            remove_duplicates_from_file(new_links_file, duplicates)
            print(f"✅ Cleaned up {new_links_file}")
    
    else:
        print("\n❌ All links are duplicates!")
        print("   You may want to find new property links to scrape.")

def remove_duplicates_from_file(filename, duplicates):
    """Remove duplicate lines from the file"""
    # Get line numbers to remove (in reverse order to avoid index shifting)
    lines_to_remove = sorted([dup[1] for dup in duplicates], reverse=True)
    
    # Read all lines
    with open(filename, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    # Remove duplicate lines
    for line_num in lines_to_remove:
        if line_num <= len(lines):
            del lines[line_num - 1]  # Convert to 0-based index
    
    # Write back to file
    with open(filename, 'w', encoding='utf-8') as f:
        f.writelines(lines)

if __name__ == "__main__":
    check_all_new_links()
