#!/bin/bash

echo "🚨 EMERGENCY: Fixing Laravel Permission Issues"
echo "=============================================="
echo ""

# Set the project directory
PROJECT_DIR="/var/www/truehold"
echo "Project directory: $PROJECT_DIR"
echo ""

# Step 1: Remove problematic files and directories
echo "1. Removing problematic files and directories..."
rm -rf "$PROJECT_DIR/storage/logs/laravel.log"
rm -rf "$PROJECT_DIR/storage/framework/cache"
rm -rf "$PROJECT_DIR/storage/framework/sessions"
rm -rf "$PROJECT_DIR/storage/framework/views"
rm -rf "$PROJECT_DIR/bootstrap/cache"
echo "   🗑️  Removed problematic files and directories"
echo ""

# Step 2: Create directories with proper permissions
echo "2. Creating directories with proper permissions..."
mkdir -p "$PROJECT_DIR/storage/app/public/rental-codes"
mkdir -p "$PROJECT_DIR/storage/logs"
mkdir -p "$PROJECT_DIR/storage/framework/cache"
mkdir -p "$PROJECT_DIR/storage/framework/sessions"
mkdir -p "$PROJECT_DIR/storage/framework/views"
mkdir -p "$PROJECT_DIR/bootstrap/cache"
echo "   ✅ Created all required directories"
echo ""

# Step 3: Set permissions
echo "3. Setting file permissions..."
chmod -R 755 "$PROJECT_DIR/storage"
chmod -R 755 "$PROJECT_DIR/bootstrap/cache"
echo "   ✅ Set directory permissions to 755"
echo ""

# Step 4: Create log file
echo "4. Creating log file..."
touch "$PROJECT_DIR/storage/logs/laravel.log"
chmod 644 "$PROJECT_DIR/storage/logs/laravel.log"
echo "   ✅ Created log file with proper permissions"
echo ""

# Step 5: Create .gitignore files for cache directories
echo "5. Creating .gitignore files for cache directories..."
echo "*" > "$PROJECT_DIR/storage/framework/cache/.gitignore"
echo "!.gitignore" >> "$PROJECT_DIR/storage/framework/cache/.gitignore"
echo "*" > "$PROJECT_DIR/storage/framework/sessions/.gitignore"
echo "!.gitignore" >> "$PROJECT_DIR/storage/framework/sessions/.gitignore"
echo "*" > "$PROJECT_DIR/storage/framework/views/.gitignore"
echo "!.gitignore" >> "$PROJECT_DIR/storage/framework/views/.gitignore"
echo "   ✅ Created .gitignore files for cache directories"
echo ""

# Step 6: Test file writing
echo "6. Testing file writing..."
echo "Test log entry at $(date)" > "$PROJECT_DIR/storage/logs/test.log"
echo "Test cache entry at $(date)" > "$PROJECT_DIR/storage/framework/cache/test.cache"
echo "Test view entry at $(date)" > "$PROJECT_DIR/storage/framework/views/test.view"

if [ -f "$PROJECT_DIR/storage/logs/test.log" ] && [ -f "$PROJECT_DIR/storage/framework/cache/test.cache" ] && [ -f "$PROJECT_DIR/storage/framework/views/test.view" ]; then
    echo "   ✅ All file writing tests passed"
    
    # Clean up test files
    rm "$PROJECT_DIR/storage/logs/test.log"
    rm "$PROJECT_DIR/storage/framework/cache/test.cache"
    rm "$PROJECT_DIR/storage/framework/views/test.view"
    echo "   🧹 Cleaned up test files"
else
    echo "   ❌ Some file writing tests failed"
fi
echo ""

# Step 7: Set ownership (if running as root)
echo "7. Setting file ownership..."
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

# Step 8: Clear Laravel caches
echo "8. Clearing Laravel caches..."
cd "$PROJECT_DIR"

if [ -f "artisan" ]; then
    php artisan config:clear 2>/dev/null && echo "   ✅ Cleared config cache" || echo "   ⚠️  Failed to clear config cache"
    php artisan cache:clear 2>/dev/null && echo "   ✅ Cleared application cache" || echo "   ⚠️  Failed to clear application cache"
    php artisan route:clear 2>/dev/null && echo "   ✅ Cleared route cache" || echo "   ⚠️  Failed to clear route cache"
    php artisan view:clear 2>/dev/null && echo "   ✅ Cleared view cache" || echo "   ⚠️  Failed to clear view cache"
    php artisan optimize:clear 2>/dev/null && echo "   ✅ Cleared all caches" || echo "   ⚠️  Failed to clear all caches"
else
    echo "   ❌ Artisan file not found"
fi
echo ""

echo "🎉 Emergency fix completed!"
echo ""
echo "📋 Summary of fixes applied:"
echo "1. ✅ Removed problematic files and directories"
echo "2. ✅ Created directories with proper permissions"
echo "3. ✅ Set file permissions (755 for dirs, 644 for files)"
echo "4. ✅ Created log file with proper permissions"
echo "5. ✅ Created .gitignore files for cache directories"
echo "6. ✅ Tested file writing capabilities"
echo "7. ✅ Set file ownership (if running as root)"
echo "8. ✅ Cleared Laravel caches"
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
echo "3. Run: chmod +x emergency_fix.sh"
echo "4. Run: ./emergency_fix.sh"
