#!/bin/bash

echo "🔧 Fixing Laravel Permissions on Live Server"
echo "============================================="
echo ""

# Set the project directory
PROJECT_DIR="/var/www/truehold"
echo "Project directory: $PROJECT_DIR"
echo ""

# Check if we're in the right directory
if [ ! -f "$PROJECT_DIR/artisan" ]; then
    echo "❌ Laravel project not found at $PROJECT_DIR"
    echo "Please update PROJECT_DIR in this script"
    exit 1
fi

echo "✅ Laravel project found"
echo ""

# Fix 1: Create storage directories
echo "1. Creating storage directories..."
mkdir -p "$PROJECT_DIR/storage/app/public/rental-codes"
mkdir -p "$PROJECT_DIR/storage/logs"
mkdir -p "$PROJECT_DIR/storage/framework/cache"
mkdir -p "$PROJECT_DIR/storage/framework/sessions"
mkdir -p "$PROJECT_DIR/storage/framework/views"
mkdir -p "$PROJECT_DIR/bootstrap/cache"
echo "   ✅ Created all required directories"
echo ""

# Fix 2: Set permissions
echo "2. Setting file permissions..."
chmod -R 755 "$PROJECT_DIR/storage"
chmod -R 755 "$PROJECT_DIR/bootstrap/cache"
echo "   ✅ Set directory permissions to 755"
echo ""

# Fix 3: Create log file
echo "3. Creating log file..."
touch "$PROJECT_DIR/storage/logs/laravel.log"
chmod 644 "$PROJECT_DIR/storage/logs/laravel.log"
echo "   ✅ Created log file with proper permissions"
echo ""

# Fix 4: Set ownership (if running as root)
echo "4. Setting file ownership..."
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

# Fix 6: Create symbolic link
echo "6. Creating symbolic link..."
cd "$PROJECT_DIR"

if [ -L "public/storage" ]; then
    echo "   ✅ Symbolic link already exists"
else
    if php artisan storage:link 2>/dev/null; then
        echo "   ✅ Created symbolic link"
    else
        echo "   ❌ Failed to create symbolic link"
        echo "   💡 Try: ln -sf $PROJECT_DIR/storage/app/public $PROJECT_DIR/public/storage"
    fi
fi
echo ""

# Fix 7: Clear Laravel caches
echo "7. Clearing Laravel caches..."
php artisan config:clear 2>/dev/null && echo "   ✅ Cleared config cache" || echo "   ⚠️  Failed to clear config cache"
php artisan cache:clear 2>/dev/null && echo "   ✅ Cleared application cache" || echo "   ⚠️  Failed to clear application cache"
php artisan route:clear 2>/dev/null && echo "   ✅ Cleared route cache" || echo "   ⚠️  Failed to clear route cache"
php artisan view:clear 2>/dev/null && echo "   ✅ Cleared view cache" || echo "   ⚠️  Failed to clear view cache"
echo ""

# Fix 8: Test Laravel functionality
echo "8. Testing Laravel functionality..."
if php artisan --version 2>/dev/null; then
    echo "   ✅ Laravel is working properly"
else
    echo "   ❌ Laravel is not working properly"
fi
echo ""

echo "🎉 Permission fix completed!"
echo ""
echo "📋 Summary of fixes applied:"
echo "1. ✅ Created storage directories"
echo "2. ✅ Set file permissions (755 for dirs, 644 for files)"
echo "3. ✅ Created log file with proper permissions"
echo "4. ✅ Set file ownership (if running as root)"
echo "5. ✅ Tested file writing"
echo "6. ✅ Created symbolic link"
echo "7. ✅ Cleared Laravel caches"
echo "8. ✅ Tested Laravel functionality"
echo ""
echo "💡 If you're still having issues:"
echo "1. Check web server error logs"
echo "2. Verify PHP-FPM or Apache user permissions"
echo "3. Check SELinux settings (if applicable)"
echo "4. Contact your hosting provider"
echo ""
echo "🔧 To run this script:"
echo "1. Upload this file to your server"
echo "2. SSH into your server"
echo "3. Run: chmod +x fix_server_permissions.sh"
echo "4. Run: ./fix_server_permissions.sh"
