# Python Setup Guide for Property Scraper

## 🚨 Current Issue
The scraper requires Python to be installed on your server. The error "python is not recognized" means Python is not installed or not in the system PATH.

## ✅ Solution

### **Option 1: Install Python on Windows Server**

1. **Download Python:**
   - Go to [python.org/downloads](https://www.python.org/downloads/)
   - Download Python 3.8 or newer for Windows
   - Choose "Windows installer (64-bit)"

2. **Install Python:**
   - Run the installer as Administrator
   - ✅ **IMPORTANT:** Check "Add Python to PATH" during installation
   - Choose "Install for all users"
   - Complete the installation

3. **Verify Installation:**
   ```cmd
   python --version
   python -m pip --version
   ```

4. **Install Required Packages:**
   ```cmd
   pip install requests beautifulsoup4 pandas lxml
   ```

### **Option 2: Use Python via Command Line**

If Python is installed but not in PATH:

1. **Find Python Installation:**
   - Usually in: `C:\Python39\` or `C:\Users\[username]\AppData\Local\Programs\Python\`
   - Look for `python.exe`

2. **Add to PATH:**
   - Open System Properties → Environment Variables
   - Add Python directory to PATH
   - Restart your server

### **Option 3: Alternative - Use PHP Scraper**

If Python installation is not possible, I can create a PHP-only scraper that doesn't require Python.

## 🔧 Troubleshooting

### **Common Issues:**

1. **"python is not recognized"**
   - Python not installed or not in PATH
   - Solution: Install Python and add to PATH

2. **"Module not found"**
   - Required packages not installed
   - Solution: Run `pip install requests beautifulsoup4 pandas lxml`

3. **Permission denied**
   - Run as Administrator
   - Check file permissions

### **Test Python Installation:**

Create a test file `test_python.php` in your project root:

```php
<?php
use Illuminate\Support\Facades\Process;

$result = Process::run('python --version');
echo "Python version: " . $result->output();
echo "Error: " . $result->errorOutput();
?>
```

## 📋 Quick Setup Commands

```bash
# Install Python packages
pip install requests beautifulsoup4 pandas lxml

# Test Python
python --version
python -c "import requests; print('Python working!')"
```

## 🎯 After Installation

1. **Restart your web server**
2. **Test the scraper interface**
3. **Add some profile URLs**
4. **Run the scraper**

The scraper will now work with your Python installation!
