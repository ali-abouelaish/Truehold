#!/usr/bin/env python3
"""
Complete Property Pipeline Runner
This script runs the entire pipeline: scraping → validation → database import preparation
"""

import os
import subprocess
import sys
import time

def run_command(command, description):
    """Run a command and handle errors"""
    print(f"\n{description}")
    print(f"Command: {command}")
    print("-" * 50)
    
    try:
        result = subprocess.run(command, shell=True, check=True, capture_output=True, text=True)
        print("Command completed successfully")
        if result.stdout:
            print("Output:")
            print(result.stdout)
        return True
    except subprocess.CalledProcessError as e:
        print(f"Command failed with exit code {e.returncode}")
        if e.stderr:
            print("Error output:")
            print(e.stderr)
        return False
    except Exception as e:
        print(f"Unexpected error: {str(e)}")
        return False

def check_file_exists(filename, description):
    """Check if a file exists and show its size"""
    if os.path.exists(filename):
        size = os.path.getsize(filename)
        print(f"{description}: {filename} ({size:,} bytes)")
        return True
    else:
        print(f"{description}: {filename} not found")
        return False

def main():
    print("Complete Property Pipeline Runner")
    print("=" * 60)
    print("This script will run the complete pipeline:")
    print("1. Scrape new property links")
    print("2. Validate and clean the data")
    print("3. Prepare for database import")
    print("=" * 60)
    
    # Check if we have the input file
    input_file = "new_links_to_scrape.csv"
    if not os.path.exists(input_file):
        print(f"❌ Input file {input_file} not found!")
        print("💡 Please ensure you have new_links_to_scrape.csv with property URLs")
        return
    
    # Check file size
    file_size = os.path.getsize(input_file)
    print(f"Input file: {input_file} ({file_size:,} bytes)")
    
    # Count URLs
    url_count = 0
    with open(input_file, 'r', encoding='utf-8') as f:
        for line in f:
            if line.strip().startswith('http'):
                url_count += 1
    
    print(f"URLs to process: {url_count}")
    
    if url_count == 0:
        print("No URLs found in input file!")
        return
    
    # Ask for confirmation
    print(f"\nThis will scrape {url_count} property pages")
    print("   Estimated time: ~2-3 minutes (with 2-second delays)")
    print("   Make sure you have a stable internet connection")
    
    confirm = input("\nContinue? (y/n): ").strip().lower()
    if confirm != 'y':
        print("Pipeline cancelled by user")
        return
    
    # Step 1: Scrape properties
    print(f"\nSTEP 1: Scraping Properties")
    print("=" * 40)
    
    if not run_command("python scrape_and_process.py", "Scraping new property links"):
        print("Scraping failed! Stopping pipeline.")
        return
    
    # Check if scraping was successful
    scraped_file = "new_properties_scraped.csv"
    if not check_file_exists(scraped_file, "Scraped data file"):
        print("Scraping did not produce output file! Stopping pipeline.")
        return
    
    # Step 2: Validate and clean data
    print(f"\nSTEP 2: Validating and Cleaning Data")
    print("=" * 40)
    
    if not run_command("python validate_and_clean.py", "Validating and cleaning scraped data"):
        print("Validation failed! Stopping pipeline.")
        return
    
    # Check if validation was successful
    cleaned_file = "new_properties_cleaned.csv"
    if not check_file_exists(cleaned_file, "Cleaned data file"):
        print("Validation did not produce output file! Stopping pipeline.")
        return
    
    # Step 3: Show final results
    print(f"\nSTEP 3: Pipeline Complete!")
    print("=" * 40)
    
    # Count final results
    final_count = 0
    with open(cleaned_file, 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        next(reader)  # Skip header
        for row in reader:
            if row and row[0].startswith('http'):
                final_count += 1
    
    print(f"Pipeline completed successfully!")
    print(f"Final results: {final_count} properties ready for import")
    
    # Show file sizes
    print(f"\nGenerated files:")
    check_file_exists(scraped_file, "Raw scraped data")
    check_file_exists(cleaned_file, "Cleaned data (ready for import)")
    
    print(f"\nNext steps:")
    print(f"   1. Review the cleaned data in {cleaned_file}")
    print(f"   2. Import into your Laravel database:")
    print(f"      php artisan properties:import {cleaned_file}")
    print(f"   3. Or manually review and edit the data first")
    
    # Optional: Show sample of cleaned data
    print(f"\nSample of cleaned data:")
    try:
        with open(cleaned_file, 'r', encoding='utf-8') as f:
            lines = f.readlines()
            if len(lines) > 1:
                print("   Header:", lines[0].strip())
                if len(lines) > 2:
                    print("   First row:", lines[1].strip()[:100] + "...")
    except Exception as e:
        print(f"   Could not read sample data: {str(e)}")

if __name__ == "__main__":
    # Import csv here to avoid import error in the function
    import csv
    main()
