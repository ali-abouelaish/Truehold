#!/usr/bin/env python3
"""
CSV Cleaner and Validator for Property Data
This script cleans corrupted CSV data and validates coordinates
"""

import csv
import re
import sys
from typing import Dict, List, Tuple

def clean_field(field: str) -> str:
    """Clean a single field by removing newlines and extra whitespace"""
    if not field:
        return ""
    
    # Remove newlines and extra whitespace
    cleaned = re.sub(r'\s+', ' ', field.strip())
    # Remove any remaining control characters
    cleaned = re.sub(r'[\x00-\x1f\x7f-\x9f]', '', cleaned)
    return cleaned

def validate_coordinates(lat: str, lng: str) -> Tuple[bool, float, float]:
    """Validate and convert coordinates"""
    try:
        # Remove any non-numeric characters except decimal points and minus signs
        lat_clean = re.sub(r'[^\d.-]', '', str(lat))
        lng_clean = re.sub(r'[^\d.-]', '', str(lng))
        
        if not lat_clean or not lng_clean:
            return False, 0.0, 0.0
        
        lat_val = float(lat_clean)
        lng_val = float(lng_clean)
        
        # Validate coordinate ranges
        if not (-90 <= lat_val <= 90):
            return False, 0.0, 0.0
        if not (-180 <= lng_val <= 180):
            return False, 0.0, 0.0
            
        return True, lat_val, lng_val
        
    except (ValueError, TypeError):
        return False, 0.0, 0.0

def clean_csv(input_file: str, output_file: str) -> Tuple[int, int, int]:
    """Clean the CSV file and return statistics"""
    
    # Define clean column headers
    clean_headers = [
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
    
    total_rows = 0
    valid_rows = 0
    invalid_coords = 0
    
    try:
        with open(input_file, 'r', encoding='utf-8', errors='ignore') as infile, \
             open(output_file, 'w', encoding='utf-8', newline='') as outfile:
            
            # Create CSV reader and writer
            reader = csv.reader(infile)
            writer = csv.writer(outfile)
            
            # Write clean headers
            writer.writerow(clean_headers)
            
            # Skip the first row (original corrupted headers)
            next(reader)
            
            for row_num, row in enumerate(reader, 1):
                total_rows += 1
                
                # Ensure we have enough columns
                while len(row) < len(clean_headers):
                    row.append("")
                
                # Truncate if too many columns
                row = row[:len(clean_headers)]
                
                # Clean all fields
                cleaned_row = [clean_field(field) for field in row]
                
                # Validate coordinates
                lat = cleaned_row[3]  # latitude column
                lng = cleaned_row[4]  # longitude column
                
                is_valid, lat_val, lng_val = validate_coordinates(lat, lng)
                
                if is_valid:
                    # Update with validated coordinates
                    cleaned_row[3] = f"{lat_val:.8f}"
                    cleaned_row[4] = f"{lng_val:.8f}"
                    valid_rows += 1
                else:
                    # Mark invalid coordinates
                    cleaned_row[3] = ""
                    cleaned_row[4] = ""
                    invalid_coords += 1
                
                # Write cleaned row
                writer.writerow(cleaned_row)
                
                # Progress indicator
                if row_num % 50 == 0:
                    print(f"Processed {row_num} rows...")
                    
    except Exception as e:
        print(f"Error processing CSV: {e}")
        return 0, 0, 0
    
    return total_rows, valid_rows, invalid_coords

def main():
    input_file = "results_detailed.csv"
    output_file = "results_cleaned.csv"
    
    print("🧹 Starting CSV cleaning process...")
    print(f"Input file: {input_file}")
    print(f"Output file: {output_file}")
    print()
    
    # Clean the CSV
    total, valid, invalid = clean_csv(input_file, output_file)
    
    print()
    print("✅ CSV cleaning completed!")
    print(f"📊 Statistics:")
    print(f"   Total rows processed: {total}")
    print(f"   Rows with valid coordinates: {valid}")
    print(f"   Rows with invalid coordinates: {invalid}")
    print(f"   Success rate: {(valid/total*100):.1f}%" if total > 0 else "   Success rate: 0%")
    print()
    print(f"📁 Cleaned data saved to: {output_file}")
    
    if invalid > 0:
        print(f"⚠️  {invalid} rows had invalid coordinates and were cleaned")
        print("   These properties won't appear on the map")

if __name__ == "__main__":
    main()
