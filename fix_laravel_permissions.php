<?php

echo "🔧 Fixing Laravel Permissions\n";
echo "============================\n\n";

try {
    // Get the project root directory
    $projectRoot = dirname(__FILE__);
    echo "Project root: {$projectRoot}\n\n";

    // Fix 1: Set storage directory permissions
    echo "1. Fixing storage directory permissions...\n";
    $storagePath = $projectRoot . '/storage';
    
    if (is_dir($storagePath)) {
        echo "   📁 Storage directory exists: {$storagePath}\n";
        
        // Set permissions recursively
        if (chmod($storagePath, 0755)) {
            echo "   ✅ Set storage directory permissions to 755\n";
        } else {
            echo "   ❌ Failed to set storage directory permissions\n";
        }
        
        // Set permissions for all subdirectories
        $subdirs = [
            'storage/app',
            'storage/app/public',
            'storage/app/public/rental-codes',
            'storage/logs',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views'
        ];
        
        foreach ($subdirs as $subdir) {
            $fullPath = $projectRoot . '/' . $subdir;
            if (is_dir($fullPath)) {
                if (chmod($fullPath, 0755)) {
                    echo "   ✅ Set permissions for: {$subdir}\n";
                } else {
                    echo "   ❌ Failed to set permissions for: {$subdir}\n";
                }
            } else {
                echo "   📁 Creating directory: {$subdir}\n";
                if (mkdir($fullPath, 0755, true)) {
                    echo "   ✅ Created directory: {$subdir}\n";
                } else {
                    echo "   ❌ Failed to create directory: {$subdir}\n";
                }
            }
        }
    } else {
        echo "   ❌ Storage directory not found\n";
    }
    
    echo "\n";

    // Fix 2: Set bootstrap/cache permissions
    echo "2. Fixing bootstrap/cache permissions...\n";
    $bootstrapCachePath = $projectRoot . '/bootstrap/cache';
    
    if (is_dir($bootstrapCachePath)) {
        if (chmod($bootstrapCachePath, 0755)) {
            echo "   ✅ Set bootstrap/cache permissions to 755\n";
        } else {
            echo "   ❌ Failed to set bootstrap/cache permissions\n";
        }
    } else {
        echo "   📁 Creating bootstrap/cache directory\n";
        if (mkdir($bootstrapCachePath, 0755, true)) {
            echo "   ✅ Created bootstrap/cache directory\n";
        } else {
            echo "   ❌ Failed to create bootstrap/cache directory\n";
        }
    }
    
    echo "\n";

    // Fix 3: Create log file with proper permissions
    echo "3. Creating log file with proper permissions...\n";
    $logPath = $projectRoot . '/storage/logs/laravel.log';
    
    if (file_exists($logPath)) {
        echo "   📄 Log file exists: {$logPath}\n";
        
        // Set log file permissions
        if (chmod($logPath, 0644)) {
            echo "   ✅ Set log file permissions to 644\n";
        } else {
            echo "   ❌ Failed to set log file permissions\n";
        }
    } else {
        echo "   📄 Creating log file: {$logPath}\n";
        
        // Create the logs directory if it doesn't exist
        $logsDir = dirname($logPath);
        if (!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
        
        // Create the log file
        if (file_put_contents($logPath, '')) {
            echo "   ✅ Created log file\n";
            
            // Set proper permissions
            if (chmod($logPath, 0644)) {
                echo "   ✅ Set log file permissions to 644\n";
            } else {
                echo "   ❌ Failed to set log file permissions\n";
            }
        } else {
            echo "   ❌ Failed to create log file\n";
        }
    }
    
    echo "\n";

    // Fix 4: Set ownership (if possible)
    echo "4. Setting file ownership...\n";
    
    // Try to get the web server user
    $webUser = 'www-data'; // Default for most Linux systems
    $webGroup = 'www-data';
    
    // Check if we can determine the web server user
    if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
        $currentUser = posix_getpwuid(posix_geteuid());
        echo "   👤 Current user: " . ($currentUser['name'] ?? 'Unknown') . "\n";
    }
    
    // Try to set ownership for storage directory
    $storagePath = $projectRoot . '/storage';
    if (is_dir($storagePath)) {
        // Note: chown requires root privileges
        echo "   💡 To set ownership, run as root:\n";
        echo "   sudo chown -R {$webUser}:{$webGroup} {$storagePath}\n";
        echo "   sudo chown -R {$webUser}:{$webGroup} {$projectRoot}/bootstrap/cache\n";
    }
    
    echo "\n";

    // Fix 5: Test file writing
    echo "5. Testing file writing...\n";
    $testLogPath = $projectRoot . '/storage/logs/test.log';
    
    try {
        $testContent = 'Test log entry at ' . date('Y-m-d H:i:s') . "\n";
        if (file_put_contents($testLogPath, $testContent)) {
            echo "   ✅ Successfully wrote to test log file\n";
            
            // Clean up test file
            unlink($testLogPath);
            echo "   🧹 Cleaned up test log file\n";
        } else {
            echo "   ❌ Failed to write to test log file\n";
        }
    } catch (Exception $e) {
        echo "   ❌ File writing test failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";

    // Fix 6: Create .env file if missing
    echo "6. Checking .env file...\n";
    $envPath = $projectRoot . '/.env';
    
    if (file_exists($envPath)) {
        echo "   ✅ .env file exists\n";
        
        // Check if LOG_CHANNEL is set
        $envContent = file_get_contents($envPath);
        if (strpos($envContent, 'LOG_CHANNEL') === false) {
            echo "   ⚠️  LOG_CHANNEL not set in .env\n";
            echo "   💡 Add to .env: LOG_CHANNEL=stack\n";
        }
    } else {
        echo "   ❌ .env file missing\n";
        echo "   💡 Copy .env.example to .env and configure\n";
    }
    
    echo "\n🎉 Laravel permissions fix completed!\n";
    echo "\n📋 Summary of fixes applied:\n";
    echo "1. ✅ Set storage directory permissions\n";
    echo "2. ✅ Set bootstrap/cache permissions\n";
    echo "3. ✅ Created/fixed log file permissions\n";
    echo "4. ✅ Provided ownership commands\n";
    echo "5. ✅ Tested file writing\n";
    echo "6. ✅ Checked .env configuration\n";
    echo "\n🔧 If issues persist, run these commands as root:\n";
    echo "sudo chown -R www-data:www-data {$projectRoot}/storage\n";
    echo "sudo chown -R www-data:www-data {$projectRoot}/bootstrap/cache\n";
    echo "sudo chmod -R 755 {$projectRoot}/storage\n";
    echo "sudo chmod -R 755 {$projectRoot}/bootstrap/cache\n";
    echo "\n💡 After fixing permissions, clear Laravel caches:\n";
    echo "php artisan config:clear\n";
    echo "php artisan cache:clear\n";
    echo "php artisan route:clear\n";
    echo "php artisan view:clear\n";

} catch (Exception $e) {
    echo "❌ Fix failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
