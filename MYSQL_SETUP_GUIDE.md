# 🗄️ MySQL Setup Guide for Property Scraper App

## 📋 **Current Status**
- ✅ **Database exported** to: `database_export_2025-08-19_10-03-22.csv` (80 properties)
- ✅ **Configuration updated** in `.env` file to use MySQL
- ✅ **Import script created**: `import_to_mysql.php`

## 🚀 **Step 1: Install MySQL**

### **Option A: Install MySQL Server (Recommended)**
1. **Download MySQL Installer** from: https://dev.mysql.com/downloads/installer/
2. **Run the installer** and choose "Developer Default" or "Server only"
3. **Set root password** (remember this!)
4. **Complete installation**

### **Option B: Use XAMPP (Easier)**
1. **Download XAMPP** from: https://www.apachefriends.org/
2. **Install XAMPP** (includes MySQL, Apache, PHP)
3. **Start MySQL service** from XAMPP Control Panel

### **Option C: Use Docker**
```bash
docker run --name mysql-property-scraper -e MYSQL_ROOT_PASSWORD=your_password -e MYSQL_DATABASE=property_scraper -p 3306:3306 -d mysql:8.0
```

## 🔧 **Step 2: Create Database**

### **Using MySQL Command Line:**
```bash
mysql -u root -p
CREATE DATABASE property_scraper CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### **Using phpMyAdmin (if using XAMPP):**
1. Open http://localhost/phpmyadmin
2. Click "New" on the left sidebar
3. Enter database name: `property_scraper`
4. Click "Create"

## ⚙️ **Step 3: Update Environment Variables**

Your `.env` file is already configured, but verify these settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=property_scraper
DB_USERNAME=root
DB_PASSWORD=your_mysql_root_password
```

**Important:** Replace `your_mysql_root_password` with your actual MySQL root password!

## 🗃️ **Step 4: Run Laravel Migrations**

```bash
php artisan migrate:fresh
```

This will create all the necessary tables in MySQL.

## 📥 **Step 5: Import Data**

```bash
php import_to_mysql.php
```

This script will:
- ✅ Test MySQL connection
- ✅ Verify table structure
- ✅ Import all 80 properties from CSV
- ✅ Clean and validate data
- ✅ Show import summary

## 🧪 **Step 6: Test the Application**

1. **Start Laravel server:**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

2. **Test map view:**
   - Open: http://localhost:8000/properties/map
   - Check console for any errors
   - Verify properties are loading

## 🔍 **Troubleshooting**

### **Connection Issues:**
- Verify MySQL service is running
- Check port 3306 is not blocked
- Confirm username/password in `.env`

### **Import Errors:**
- Ensure CSV file exists: `database_export_2025-08-19_10-03-22.csv`
- Check table structure with: `php artisan migrate:status`
- Verify database permissions

### **Performance Issues:**
- MySQL is generally faster than SQLite for larger datasets
- Consider adding indexes to frequently queried columns
- Monitor query performance with Laravel Debugbar

## 📊 **Expected Results**

After successful migration:
- ✅ **80 properties** imported to MySQL
- ✅ **Map view working** with all properties
- ✅ **Better performance** than SQLite
- ✅ **Scalability** for future growth

## 🗂️ **File Summary**

- `database_export_2025-08-19_10-03-22.csv` - Your exported data (80 properties)
- `import_to_mysql.php` - Import script for MySQL
- `.env` - Updated configuration for MySQL
- `MYSQL_SETUP_GUIDE.md` - This guide

## 🆘 **Need Help?**

If you encounter issues:
1. Check MySQL service status
2. Verify database connection
3. Review Laravel logs: `storage/logs/laravel.log`
4. Ensure all environment variables are set correctly

---

**Next Steps:** Once MySQL is installed and configured, run the import script to migrate your data!
