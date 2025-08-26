#!/usr/bin/env python3
"""
Fix CSV Headers for Laravel Import
This script reorders the CSV columns to match the expected Laravel import format.
"""

import csv
import os

def fix_csv_headers():
    """Fix CSV headers to match Laravel import expectations"""
    
    input_file = "new_properties_cleaned.csv"
    output_file = "new_properties_for_import.csv"
    
    # Expected headers in Laravel ImportProperties command
    expected_headers = [
        'url',
        'title', 
        'location',
        'latitude',
        'longitude',
        'price',
        'description',
        'property_type',
        'available_date',
        'photo_count',
        'first_photo_url',
        'all_photos',
        'contact_info',
        'management_company',
        'amenities'
    ]
    
    if not os.path.exists(input_file):
        print(f"Input file {input_file} not found!")
        return
    
    print(f"Fixing CSV headers for Laravel import...")
    print(f"Input: {input_file}")
    print(f"Output: {output_file}")
    
    # Read the cleaned CSV
    properties = []
    with open(input_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            properties.append(row)
    
    print(f"Found {len(properties)} properties")
    
    # Create new CSV with correct headers
    with open(output_file, 'w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=expected_headers)
        writer.writeheader()
        
        for prop in properties:
            # Create new row with only expected fields
            new_row = {}
            for header in expected_headers:
                new_row[header] = prop.get(header, '')
            writer.writerow(new_row)
    
    print(f"CSV fixed successfully!")
    print(f"Headers: {', '.join(expected_headers)}")
    print(f"Properties: {len(properties)}")
    print(f"Ready for Laravel import: {output_file}")

if __name__ == "__main__":
    fix_csv_headers()
