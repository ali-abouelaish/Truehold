#!/usr/bin/env python3
"""
Restore and Merge Script
This script restores the original results_cleaned.csv from backup and then properly merges it with new scraped data.
"""

import csv
import os
import shutil

def restore_and_merge():
    """Restore original data and merge with new data"""
    
    backup_file = "database_backups/properties_backup_20250817_231454.csv"
    new_data_file = "new_properties_for_import_exact.csv"
    restored_file = "results_cleaned_restored.csv"
    final_merged_file = "all_properties_final.csv"
    
    print("Restoring and merging property data...")
    
    # Step 1: Restore the original results_cleaned.csv from backup
    if not os.path.exists(backup_file):
        print(f"Backup file {backup_file} not found!")
        return
    
    print(f"Step 1: Restoring original data from backup...")
    
    # Read the backup data
    backup_properties = []
    with open(backup_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            backup_properties.append(row)
    
    print(f"Found {len(backup_properties)} properties in backup")
    
    # Convert backup data to the expected format (remove Laravel-specific fields)
    expected_headers = [
        'url', 'title', 'location', 'latitude', 'longitude', 'price', 'description',
        'property_type', 'available_date', 'photo_count', 'first_photo_url', 'all_photos',
        'contact_info', 'management_company', 'amenities', 'age', 'ages', 'any_pets',
        'available', 'balcony_roof_terrace', 'bills_included', 'broadband_included',
        'couples_allowed', 'deposit', 'deposit_room_1', 'deposit_room_2', 'deposit_room_3',
        'deposit_room_4', 'disabled_access', 'furnishings', 'garage', 'garden_patio',
        'gender', 'living_room', 'max_age', 'maximum_term', 'min_age', 'minimum_term',
        'number_housemates', 'number_flatmates', 'occupation', 'parking', 'pets_allowed',
        'price_pcm', 'references', 'room_type', 'smalloweding_allowed', 'smoker',
        'total_number_rooms', 'vegetarian_vegan'
    ]
    
    # Create restored properties with proper headers
    restored_properties = []
    for prop in backup_properties:
        restored_prop = {}
        for header in expected_headers:
            if header in prop:
                restored_prop[header] = prop[header]
            else:
                restored_prop[header] = ''
        restored_properties.append(restored_prop)
    
    # Write restored file
    with open(restored_file, 'w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=expected_headers)
        writer.writeheader()
        for prop in restored_properties:
            writer.writerow(prop)
    
    print(f"Restored {len(restored_properties)} properties to {restored_file}")
    
    # Step 2: Read new scraped data
    if not os.path.exists(new_data_file):
        print(f"New data file {new_data_file} not found!")
        return
    
    print(f"Step 2: Reading new scraped data...")
    
    new_properties = []
    with open(new_data_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            new_properties.append(row)
    
    print(f"Found {len(new_properties)} new properties")
    
    # Step 3: Merge data, avoiding duplicates
    print(f"Step 3: Merging data and removing duplicates...")
    
    # Create a set of existing URLs to avoid duplicates
    existing_urls = set()
    for prop in restored_properties:
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
    all_properties = restored_properties + unique_new_properties
    
    print(f"Total properties to import: {len(all_properties)}")
    
    # Write final merged file
    if all_properties:
        with open(final_merged_file, 'w', newline='', encoding='utf-8') as f:
            writer = csv.DictWriter(f, fieldnames=expected_headers)
            writer.writeheader()
            
            for prop in all_properties:
                writer.writerow(prop)
        
        print(f"Successfully created final merged file: {final_merged_file}")
        print(f"Ready for import with {len(all_properties)} properties")
        
        # Show breakdown
        print(f"\nFinal Breakdown:")
        print(f"  - Restored original properties: {len(restored_properties)}")
        print(f"  - New unique properties: {len(unique_new_properties)}")
        print(f"  - Duplicates filtered out: {duplicates_found}")
        print(f"  - Total for import: {len(all_properties)}")
        
        # Also restore the original results_cleaned.csv
        shutil.copy2(restored_file, "results_cleaned.csv")
        print(f"Restored original results_cleaned.csv with {len(restored_properties)} properties")
        
    else:
        print("No properties to merge!")

if __name__ == "__main__":
    restore_and_merge()
