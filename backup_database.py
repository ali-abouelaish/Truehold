#!/usr/bin/env python3
"""
Database Backup Script for Property Scraper App
This script creates backups before importing new properties to ensure data safety.
"""

import os
import shutil
import sqlite3
import csv
from datetime import datetime
import subprocess

class DatabaseBackup:
    def __init__(self):
        self.backup_dir = "database_backups"
        self.timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        self.db_path = "database/database.sqlite"
        self.backup_path = f"{self.backup_dir}/database_backup_{self.timestamp}.sqlite"
        self.csv_backup_path = f"{self.backup_dir}/properties_backup_{self.timestamp}.csv"
        
    def create_backup_directory(self):
        """Create backup directory if it doesn't exist"""
        if not os.path.exists(self.backup_dir):
            os.makedirs(self.backup_dir)
            print(f"📁 Created backup directory: {self.backup_dir}")
        else:
            print(f"📁 Using existing backup directory: {self.backup_dir}")
    
    def backup_sqlite_database(self):
        """Create a copy of the SQLite database file"""
        if not os.path.exists(self.db_path):
            print(f"❌ Database file not found: {self.db_path}")
            return False
        
        try:
            # Create backup
            shutil.copy2(self.db_path, self.backup_path)
            
            # Verify backup
            if os.path.exists(self.backup_path):
                original_size = os.path.getsize(self.db_path)
                backup_size = os.path.getsize(self.backup_path)
                
                print(f"✅ Database backup created successfully!")
                print(f"   Original: {self.db_path} ({original_size:,} bytes)")
                print(f"   Backup: {self.backup_path} ({backup_size:,} bytes)")
                
                if original_size == backup_size:
                    print(f"   ✅ Backup size matches original")
                    return True
                else:
                    print(f"   ⚠️  Backup size differs from original")
                    return False
            else:
                print(f"❌ Backup file was not created")
                return False
                
        except Exception as e:
            print(f"❌ Error creating database backup: {str(e)}")
            return False
    
    def export_properties_to_csv(self):
        """Export current properties from database to CSV as additional backup"""
        if not os.path.exists(self.db_path):
            print(f"❌ Database file not found: {self.db_path}")
            return False
        
        try:
            # Connect to database
            conn = sqlite3.connect(self.db_path)
            cursor = conn.cursor()
            
            # Get table structure
            cursor.execute("PRAGMA table_info(properties)")
            columns = cursor.fetchall()
            column_names = [col[1] for col in columns]
            
            # Get all properties
            cursor.execute("SELECT * FROM properties")
            properties = cursor.fetchall()
            
            # Write to CSV
            with open(self.csv_backup_path, 'w', newline='', encoding='utf-8') as csvfile:
                writer = csv.writer(csvfile)
                writer.writerow(column_names)
                writer.writerows(properties)
            
            conn.close()
            
            if os.path.exists(self.csv_backup_path):
                file_size = os.path.getsize(self.csv_backup_path)
                print(f"✅ Properties exported to CSV successfully!")
                print(f"   CSV backup: {self.csv_backup_path} ({file_size:,} bytes)")
                print(f"   Properties exported: {len(properties)}")
                return True
            else:
                print(f"❌ CSV backup file was not created")
                return False
                
        except Exception as e:
            print(f"❌ Error exporting properties to CSV: {str(e)}")
            return False
    
    def show_database_info(self):
        """Show information about the current database"""
        if not os.path.exists(self.db_path):
            print(f"❌ Database file not found: {self.db_path}")
            return
        
        try:
            # Connect to database
            conn = sqlite3.connect(self.db_path)
            cursor = conn.cursor()
            
            # Get table info
            cursor.execute("SELECT name FROM sqlite_master WHERE type='table'")
            tables = cursor.fetchall()
            
            print(f"\n📊 Current Database Information:")
            print(f"   Database file: {self.db_path}")
            print(f"   File size: {os.path.getsize(self.db_path):,} bytes")
            print(f"   Tables: {', '.join([table[0] for table in tables])}")
            
            # Get properties count
            try:
                cursor.execute("SELECT COUNT(*) FROM properties")
                properties_count = cursor.fetchone()[0]
                print(f"   Properties in database: {properties_count}")
            except:
                print(f"   Properties table: Not found or empty")
            
            # Get users count
            try:
                cursor.execute("SELECT COUNT(*) FROM users")
                users_count = cursor.fetchone()[0]
                print(f"   Users in database: {users_count}")
            except:
                print(f"   Users table: Not found or empty")
            
            conn.close()
            
        except Exception as e:
            print(f"❌ Error reading database info: {str(e)}")
    
    def show_backup_summary(self):
        """Show summary of all backups"""
        if not os.path.exists(self.backup_dir):
            print(f"📁 No backup directory found")
            return
        
        print(f"\n📁 Backup Summary:")
        print(f"   Backup directory: {self.backup_dir}")
        
        # List all backup files
        backup_files = []
        for file in os.listdir(self.backup_dir):
            if file.endswith('.sqlite') or file.endswith('.csv'):
                file_path = os.path.join(self.backup_dir, file)
                file_size = os.path.getsize(file_path)
                backup_files.append((file, file_size, os.path.getmtime(file_path)))
        
        if backup_files:
            # Sort by modification time (newest first)
            backup_files.sort(key=lambda x: x[2], reverse=True)
            
            for file, size, mtime in backup_files:
                mtime_str = datetime.fromtimestamp(mtime).strftime("%Y-%m-%d %H:%M:%S")
                print(f"   {file} ({size:,} bytes) - {mtime_str}")
        else:
            print(f"   No backup files found")
    
    def run_complete_backup(self):
        """Run the complete backup process"""
        print("🏠 Property Scraper App - Database Backup")
        print("=" * 60)
        print("This script will create backups before importing new properties")
        print("=" * 60)
        
        # Show current database info
        self.show_database_info()
        
        # Create backup directory
        self.create_backup_directory()
        
        # Create database backup
        print(f"\n📋 STEP 1: Creating Database Backup")
        print("-" * 40)
        if not self.backup_sqlite_database():
            print("❌ Database backup failed! Cannot proceed safely.")
            return False
        
        # Export properties to CSV
        print(f"\n📋 STEP 2: Exporting Properties to CSV")
        print("-" * 40)
        if not self.export_properties_to_csv():
            print("⚠️  CSV export failed, but database backup was successful")
            print("   You can still proceed with the database backup")
        
        # Show backup summary
        print(f"\n📋 STEP 3: Backup Summary")
        print("-" * 40)
        self.show_backup_summary()
        
        print(f"\n🎉 Backup completed successfully!")
        print(f"💡 You can now safely proceed with importing new properties")
        print(f"   If anything goes wrong, restore from: {self.backup_path}")
        
        return True

def main():
    backup = DatabaseBackup()
    
    try:
        success = backup.run_complete_backup()
        if success:
            print(f"\n✅ Ready to proceed with property import!")
            print(f"   Next step: Run your scraping and import pipeline")
        else:
            print(f"\n❌ Backup failed! Please resolve issues before proceeding")
            
    except KeyboardInterrupt:
        print(f"\n⚠️  Backup interrupted by user")
    except Exception as e:
        print(f"\n❌ Unexpected error during backup: {str(e)}")

if __name__ == "__main__":
    main()
