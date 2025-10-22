#!/bin/bash

echo "🔧 Fixing Laravel Permissions"
echo "============================"
echo ""

# Get the project directory
PROJECT_DIR="/var/www/truehold"
echo "Project directory: $PROJECT_DIR"
echo ""

# Fix 1: Set storage directory permissions
echo "1. Fixing storage directory permissions..."
if [ -d "$PROJECT_DIR/storage" ]; then
    echo "   📁 Storage directory exists"
    chmod -R 755 "$PROJECT_DIR/storage"
    echo "   ✅ Set storage directory permissions to 755"
    
    # Create missing subdirectories
    mkdir -p "$PROJECT_DIR/storage/app/public/rental-codes"
    mkdir -p "$PROJECT_DIR/storage/logs"
    mkdir -p "$PROJECT_DIR/storage/framework/cache"
    mkdir -p "$PROJECT_DIR/storage/framework/sessions"
    mkdir -p "$PROJECT_DIR/storage/framework/views"
    echo "   ✅ Created missing subdirectories"
else
    echo "   ❌ Storage directory not found"
fi

echo ""

# Fix 2: Set bootstrap/cache permissions
echo "2. Fixing bootstrap/cache permissions..."
if [ -d "$PROJECT_DIR/bootstrap/cache" ]; then
    chmod -R 755 "$PROJECT_DIR/bootstrap/cache"
    echo "   ✅ Set bootstrap/cache permissions to 755"
else
    mkdir -p "$PROJECT_DIR/bootstrap/cache"
    chmod -R 755 "$PROJECT_DIR/bootstrap/cache"
    echo "   ✅ Created and set bootstrap/cache permissions"
fi

echo ""

# Fix 3: Create log file with proper permissions
echo "3. Creating log file with proper permissions..."
LOG_FILE="$PROJECT_DIR/storage/logs/laravel.log"

if [ -f "$LOG_FILE" ]; then
    echo "   📄 Log file exists"
    chmod 644 "$LOG_FILE"
    echo "   ✅ Set log file permissions to 644"
else
    echo "   📄 Creating log file"
    touch "$LOG_FILE"
    chmod 644 "$LOG_FILE"
    echo "   ✅ Created log file with permissions 644"
fi

echo ""

# Fix 4: Set ownership
echo "4. Setting file ownership..."
echo "   👤 Setting ownership to www-data:www-data"

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    chown -R www-data:www-data "$PROJECT_DIR/storage"
    chown -R www-data:www-data "$PROJECT_DIR/bootstrap/cache"
    echo "   ✅ Set ownership to www-data:www-data"
else
    echo "   ⚠️  Not running as root - ownership not changed"
    echo "   💡 Run as root to set ownership:"
    echo "   sudo chown -R www-data:www-data $PROJECT_DIR/storage"
    echo "   sudo chown -R www-data:www-data $PROJECT_DIR/bootstrap/cache"
fi

echo ""

# Fix 5: Test file writing
echo "5. Testing file writing..."
TEST_LOG="$PROJECT_DIR/storage/logs/test.log"
echo "Test log entry at $(date)" > "$TEST_LOG"

if [ -f "$TEST_LOG" ]; then
    echo "   ✅ Successfully wrote to test log file"
    rm "$TEST_LOG"
    echo "   🧹 Cleaned up test log file"
else
    echo "   ❌ Failed to write to test log file"
fi

echo ""

# Fix 6: Clear Laravel caches
echo "6. Clearing Laravel caches..."
cd "$PROJECT_DIR"

if [ -f "artisan" ]; then
    php artisan config:clear 2>/dev/null && echo "   ✅ Cleared config cache" || echo "   ⚠️  Failed to clear config cache"
    php artisan cache:clear 2>/dev/null && echo "   ✅ Cleared application cache" || echo "   ⚠️  Failed to clear application cache"
    php artisan route:clear 2>/dev/null && echo "   ✅ Cleared route cache" || echo "   ⚠️  Failed to clear route cache"
    php artisan view:clear 2>/dev/null && echo "   ✅ Cleared view cache" || echo "   ⚠️  Failed to clear view cache"
else
    echo "   ❌ Artisan file not found"
fi

echo ""
echo "🎉 Laravel permissions fix completed!"
echo ""
echo "📋 Summary of fixes applied:"
echo "1. ✅ Set storage directory permissions"
echo "2. ✅ Set bootstrap/cache permissions"
echo "3. ✅ Created/fixed log file permissions"
echo "4. ✅ Set file ownership (if running as root)"
echo "5. ✅ Tested file writing"
echo "6. ✅ Cleared Laravel caches"
echo ""
echo "💡 If you're still having issues:"
echo "1. Check web server error logs"
echo "2. Verify PHP-FPM or Apache user permissions"
echo "3. Check SELinux settings (if applicable)"
echo "4. Contact your hosting provider"
