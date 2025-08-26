#!/usr/bin/env python3
"""
Property Data Validation and Cleaning
This script validates and cleans scraped property data before database import.
"""

import csv
import os
import re
from datetime import datetime

class PropertyDataValidator:
    def __init__(self):
        self.validation_errors = []
        self.cleaned_data = []
        
    def validate_and_clean_csv(self, input_file, output_file):
        """Validate and clean property data from CSV file"""
        print(f"Validating and cleaning property data from {input_file}")
        print("=" * 60)
        
        if not os.path.exists(input_file):
            print(f"❌ Input file {input_file} not found!")
            return None
        
        # Read and validate data
        properties = []
        with open(input_file, 'r', encoding='utf-8') as f:
            reader = csv.DictReader(f)
            for row_num, row in enumerate(reader, 2):  # Start from 2 (after header)
                cleaned_row = self.validate_and_clean_property(row, row_num)
                if cleaned_row:
                    properties.append(cleaned_row)
        
        # Save cleaned data
        if properties:
            self.save_cleaned_data(properties, output_file)
            print(f"\nValidation and cleaning completed!")
            print(f"Total properties processed: {len(properties)}")
            print(f"Cleaned data saved to: {output_file}")
            
            if self.validation_errors:
                print(f"\nValidation warnings: {len(self.validation_errors)}")
                for error in self.validation_errors[:10]:  # Show first 10
                    print(f"   {error}")
                if len(self.validation_errors) > 10:
                    print(f"   ... and {len(self.validation_errors) - 10} more warnings")
            
            return output_file
        else:
            print("❌ No valid properties found!")
            return None
    
    def validate_and_clean_property(self, row, row_num):
        """Validate and clean a single property row"""
        cleaned = {}
        
        # Essential fields validation
        if not row.get('url') or not row['url'].startswith('http'):
            self.validation_errors.append(f"Row {row_num}: Invalid or missing URL")
            return None
        
        # Clean and validate each field
        for field, value in row.items():
            cleaned[field] = self.clean_field_value(field, value, row_num)
        
        # Additional validation
        self.validate_coordinates(cleaned, row_num)
        self.validate_price(cleaned, row_num)
        self.validate_dates(cleaned, row_num)
        
        return cleaned
    
    def clean_field_value(self, field, value, row_num):
        """Clean individual field values"""
        if value is None:
            return ''
        
        value = str(value).strip()
        
        # Handle empty values
        if value.lower() in ['n/a', 'none', 'null', 'undefined', 'not found', 'not available']:
            return ''
        
        # Field-specific cleaning
        if field == 'url':
            return self.clean_url(value)
        elif field in ['latitude', 'longitude']:
            return self.clean_coordinate(value)
        elif field == 'price':
            return self.clean_price(value)
        elif field == 'photo_count':
            return self.clean_photo_count(value)
        elif field in ['age', 'ages', 'min_age', 'max_age']:
            return self.clean_age(value)
        elif field in ['deposit', 'deposit_room_1', 'deposit_room_2', 'deposit_room_3', 'deposit_room_4']:
            return self.clean_deposit(value)
        elif field in ['minimum_term', 'maximum_term']:
            return self.clean_term(value)
        elif field in ['number_housemates', 'number_flatmates', 'total_number_rooms']:
            return self.clean_number(value)
        elif field in ['bills_included', 'broadband_included', 'couples_allowed', 'any_pets', 'pets_allowed']:
            return self.clean_boolean(value)
        elif field in ['furnishings', 'disabled_access', 'garage', 'garden_patio', 'parking']:
            return self.clean_boolean(value)
        elif field in ['balcony_roof_terrace', 'living_room', 'smoking_allowed', 'smoker']:
            return self.clean_boolean(value)
        else:
            return value
    
    def clean_url(self, url):
        """Clean URL by removing UTM parameters"""
        if not url or not url.startswith('http'):
            return url
        
        try:
            from urllib.parse import urlparse, parse_qs
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
    
    def clean_coordinate(self, coord):
        """Clean coordinate values"""
        if not coord:
            return ''
        
        try:
            # Remove any non-numeric characters except decimal point and minus
            cleaned = re.sub(r'[^\d.-]', '', coord)
            if cleaned and cleaned != '-':
                # Validate coordinate range
                coord_float = float(cleaned)
                if -90 <= coord_float <= 90:  # Latitude range
                    return str(coord_float)
                elif -180 <= coord_float <= 180:  # Longitude range
                    return str(coord_float)
            return ''
        except:
            return ''
    
    def clean_price(self, price):
        """Clean price values"""
        if not price:
            return ''
        
        try:
            # Extract numeric value
            price_match = re.search(r'[£$]?([\d,]+)', price)
            if price_match:
                # Remove commas and convert to integer
                clean_price = price_match.group(1).replace(',', '')
                if clean_price.isdigit():
                    return clean_price
            return ''
        except:
            return ''
    
    def clean_photo_count(self, count):
        """Clean photo count values"""
        if not count:
            return '0'
        
        try:
            count_int = int(count)
            if count_int >= 0:
                return str(count_int)
            return '0'
        except:
            return '0'
    
    def clean_age(self, age):
        """Clean age values"""
        if not age:
            return ''
        
        try:
            # Extract numeric age
            age_match = re.search(r'(\d+)', age)
            if age_match:
                age_int = int(age_match.group(1))
                if 16 <= age_int <= 100:  # Reasonable age range
                    return str(age_int)
            return ''
        except:
            return ''
    
    def clean_deposit(self, deposit):
        """Clean deposit values"""
        if not deposit:
            return ''
        
        try:
            # Extract numeric value
            deposit_match = re.search(r'[£$]?([\d,]+)', deposit)
            if deposit_match:
                clean_deposit = deposit_match.group(1).replace(',', '')
                if clean_deposit.isdigit():
                    return clean_deposit
            return ''
        except:
            return ''
    
    def clean_term(self, term):
        """Clean term values"""
        if not term:
            return ''
        
        # Standardize common terms
        term_lower = term.lower()
        if 'month' in term_lower:
            # Extract number of months
            month_match = re.search(r'(\d+)', term)
            if month_match:
                return f"{month_match.group(1)} months"
        elif 'year' in term_lower:
            # Extract number of years
            year_match = re.search(r'(\d+)', term)
            if year_match:
                return f"{year_match.group(1)} years"
        elif 'none' in term_lower or 'unlimited' in term_lower:
            return 'None'
        
        return term
    
    def clean_number(self, number):
        """Clean numeric values"""
        if not number:
            return ''
        
        try:
            # Extract numeric value
            num_match = re.search(r'(\d+)', number)
            if num_match:
                num_int = int(num_match.group(1))
                if num_int >= 0:
                    return str(num_int)
            return ''
        except:
            return ''
    
    def clean_boolean(self, value):
        """Clean boolean-like values"""
        if not value:
            return 'No'
        
        value_lower = value.lower()
        if value_lower in ['yes', 'true', '1', 'available', 'included', 'allowed']:
            return 'Yes'
        elif value_lower in ['no', 'false', '0', 'not available', 'not included', 'not allowed']:
            return 'No'
        elif value_lower in ['unknown', 'unsure', 'maybe']:
            return 'Unknown'
        else:
            return value
    
    def validate_coordinates(self, row, row_num):
        """Validate coordinate pairs"""
        lat = row.get('latitude', '')
        lng = row.get('longitude', '')
        
        if lat and lng:
            try:
                lat_float = float(lat)
                lng_float = float(lng)
                
                if not (-90 <= lat_float <= 90):
                    self.validation_errors.append(f"Row {row_num}: Invalid latitude {lat}")
                    row['latitude'] = ''
                
                if not (-180 <= lng_float <= 180):
                    self.validation_errors.append(f"Row {row_num}: Invalid longitude {lng}")
                    row['longitude'] = ''
                    
            except ValueError:
                self.validation_errors.append(f"Row {row_num}: Invalid coordinate format")
                row['latitude'] = ''
                row['longitude'] = ''
    
    def validate_price(self, row, row_num):
        """Validate price values"""
        price = row.get('price', '')
        if price and not price.isdigit():
            self.validation_errors.append(f"Row {row_num}: Invalid price format '{price}'")
            row['price'] = ''
    
    def validate_dates(self, row, row_num):
        """Validate date values"""
        available_date = row.get('available_date', '')
        if available_date and available_date.lower() not in ['now', 'immediate', 'available now']:
            # Try to parse date
            try:
                # Add basic date validation here if needed
                pass
            except:
                self.validation_errors.append(f"Row {row_num}: Invalid date format '{available_date}'")
    
    def save_cleaned_data(self, properties, output_file):
        """Save cleaned property data to CSV"""
        if not properties:
            return
        
        # Get all field names
        all_fields = set()
        for prop in properties:
            all_fields.update(prop.keys())
        
        # Ensure all properties have all fields
        for prop in properties:
            for field in all_fields:
                if field not in prop:
                    prop[field] = ''
        
        # Sort fields for consistent output
        sorted_fields = sorted(all_fields)
        
        with open(output_file, 'w', newline='', encoding='utf-8') as f:
            writer = csv.DictWriter(f, fieldnames=sorted_fields)
            writer.writeheader()
            writer.writerows(properties)

def main():
    print("Property Data Validation and Cleaning")
    print("=" * 60)
    
    input_file = "new_properties_scraped.csv"
    output_file = "new_properties_cleaned.csv"
    
    if not os.path.exists(input_file):
        print(f"Scraped data file {input_file} not found!")
        print("Please run the scraping script first: python scrape_and_process.py")
        return
    
    # Initialize validator
    validator = PropertyDataValidator()
    
    # Validate and clean data
    try:
        result_file = validator.validate_and_clean_csv(input_file, output_file)
        
        if result_file:
            print(f"\nData validation and cleaning completed!")
            print(f"Cleaned data saved to: {result_file}")
            
            print(f"\nNext steps:")
            print(f"   1. Review the cleaned data in {result_file}")
            print(f"   2. Import into your Laravel database")
            print(f"   3. Use: php artisan properties:import {result_file}")
            
            # Show summary
            print(f"\nSummary:")
            print(f"   Input file: {input_file}")
            print(f"   Output file: {result_file}")
            print(f"   Validation warnings: {len(validator.validation_errors)}")
        
    except Exception as e:
        print(f"\nError during validation: {str(e)}")

if __name__ == "__main__":
    main()
