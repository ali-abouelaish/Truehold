#!/usr/bin/env python3
"""
Merge and Import Script
This script merges the new scraped data with the existing cleaned data to create a comprehensive import file.
"""

import csv
import os

def merge_csv_files():
    """Merge new scraped data with existing cleaned data"""
    
    new_data_file = "new_properties_for_import_exact.csv"
    existing_data_file = "results_cleaned.csv"
    merged_file = "all_properties_merged.csv"
    
    # Check if both files exist
    if not os.path.exists(new_data_file):
        print(f"New data file {new_data_file} not found!")
        return
    
    if not os.path.exists(existing_data_file):
        print(f"Existing data file {existing_data_file} not found!")
        return
    
    print(f"Merging CSV files...")
    print(f"New data: {new_data_file}")
    print(f"Existing data: {existing_data_file}")
    print(f"Output: {merged_file}")
    
    # Read new data
    new_properties = []
    with open(new_data_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            new_properties.append(row)
    
    print(f"Found {len(new_properties)} new properties")
    
    # Read existing data
    existing_properties = []
    with open(existing_data_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            existing_properties.append(row)
    
    print(f"Found {len(existing_properties)} existing properties")
    
    # Create a set of existing URLs to avoid duplicates
    existing_urls = set()
    for prop in existing_properties:
        url = prop.get('url', '').strip()
        if url:
            existing_urls.add(url)
    
    # Filter new properties to avoid duplicates
    unique_new_properties = []
    duplicates_found = 0
    
    for prop in new_properties:
        url = prop.get('url', '').strip()
        if url and url not in existing_urls:
            unique_new_properties.append(prop)
        else:
            duplicates_found += 1
    
    print(f"Found {duplicates_found} duplicate URLs in new data")
    print(f"Adding {len(unique_new_properties)} unique new properties")
    
    # Combine all properties
    all_properties = existing_properties + unique_new_properties
    
    print(f"Total properties to import: {len(all_properties)}")
    
    # Write merged file
    if all_properties:
        fieldnames = all_properties[0].keys()
        
        with open(merged_file, 'w', newline='', encoding='utf-8') as f:
            writer = csv.DictWriter(f, fieldnames=fieldnames)
            writer.writeheader()
            
            for prop in all_properties:
                writer.writerow(prop)
        
        print(f"Successfully created merged file: {merged_file}")
        print(f"Ready for import with {len(all_properties)} properties")
        
        # Show breakdown
        print(f"\nBreakdown:")
        print(f"  - Existing properties: {len(existing_properties)}")
        print(f"  - New unique properties: {len(unique_new_properties)}")
        print(f"  - Duplicates filtered out: {duplicates_found}")
        print(f"  - Total for import: {len(all_properties)}")
        
    else:
        print("No properties to merge!")

if __name__ == "__main__":
    merge_csv_files()
