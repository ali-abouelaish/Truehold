#!/usr/bin/env python3
"""
Fix CSV Headers to Match Exact Database Schema
This script reorders the CSV columns to match the exact headers expected by the database.
"""

import csv
import os

def fix_csv_headers_exact():
    """Fix CSV headers to match exact database schema"""
    
    input_file = "new_properties_scraped.csv"
    output_file = "new_properties_for_import_exact.csv"
    
    # Exact headers expected by the database
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
        'amenities',
        'age',
        'ages',
        'any_pets',
        'available',
        'balcony_roof_terrace',
        'bills_included',
        'broadband_included',
        'couples_allowed',
        'deposit',
        'deposit_room_1',
        'deposit_room_2',
        'deposit_room_3',
        'deposit_room_4',
        'disabled_access',
        'furnishings',
        'garage',
        'garden_patio',
        'gender',
        'living_room',
        'max_age',
        'maximum_term',
        'min_age',
        'minimum_term',
        'number_housemates',
        'number_flatmates',
        'occupation',
        'parking',
        'pets_allowed',
        'price_pcm',
        'references',
        'room_type',
        'smalloweding_allowed',
        'smoker',
        'total_number_rooms',
        'vegetarian_vegan'
    ]
    
    if not os.path.exists(input_file):
        print(f"Input file {input_file} not found!")
        return
    
    print(f"Fixing CSV headers to match exact database schema...")
    print(f"Input: {input_file}")
    print(f"Output: {output_file}")
    
    # Read the scraped CSV
    properties = []
    with open(input_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            properties.append(row)
    
    print(f"Found {len(properties)} properties")
    
    # Create new CSV with exact headers
    with open(output_file, 'w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=expected_headers)
        writer.writeheader()
        
        for prop in properties:
            # Create new row with only expected fields, filling missing ones with empty strings
            new_row = {}
            for header in expected_headers:
                new_row[header] = prop.get(header, '')
            writer.writerow(new_row)
    
    print(f"CSV fixed successfully!")
    print(f"Headers: {', '.join(expected_headers)}")
    print(f"Properties: {len(properties)}")
    print(f"Ready for database import: {output_file}")
    
    # Show sample of first property
    if properties:
        print(f"\nSample of first property:")
        first_prop = properties[0]
        for header in expected_headers[:10]:  # Show first 10 fields
            value = first_prop.get(header, '')
            if value and value != 'N/A':
                print(f"  {header}: {value[:50]}...")
        print(f"  ... and {len(expected_headers) - 10} more fields")

if __name__ == "__main__":
    fix_csv_headers_exact()
