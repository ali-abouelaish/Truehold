<?php

echo "🔧 Fixing Live Server Laravel Permissions\n";
echo "========================================\n\n";

try {
    // Get the project root directory
    $projectRoot = '/var/www/truehold';
    echo "Project root: {$projectRoot}\n\n";

    // Fix 1: Create storage directories with proper permissions
    echo "1. Creating storage directories...\n";
    $directories = [
        'storage',
        'storage/app',
        'storage/app/public',
        'storage/app/public/rental-codes',
        'storage/logs',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'bootstrap/cache'
    ];
    
    foreach ($directories as $dir) {
        $fullPath = $projectRoot . '/' . $dir;
        if (!is_dir($fullPath)) {
            if (mkdir($fullPath, 0755, true)) {
                echo "   ✅ Created directory: {$dir}\n";
            } else {
                echo "   ❌ Failed to create directory: {$dir}\n";
            }
        } else {
            echo "   ✅ Directory exists: {$dir}\n";
        }
    }
    
    echo "\n";

    // Fix 2: Set proper permissions
    echo "2. Setting file permissions...\n";
    
    // Set storage permissions
    if (is_dir($projectRoot . '/storage')) {
        chmod($projectRoot . '/storage', 0755);
        echo "   ✅ Set storage directory permissions\n";
        
        // Set permissions recursively for storage
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($projectRoot . '/storage'),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                chmod($item->getPathname(), 0755);
            } else {
                chmod($item->getPathname(), 0644);
            }
        }
        echo "   ✅ Set recursive permissions for storage\n";
    }
    
    // Set bootstrap/cache permissions
    if (is_dir($projectRoot . '/bootstrap/cache')) {
        chmod($projectRoot . '/bootstrap/cache', 0755);
        echo "   ✅ Set bootstrap/cache permissions\n";
    }
    
    echo "\n";

    // Fix 3: Create log file with proper permissions
    echo "3. Creating log file...\n";
    $logPath = $projectRoot . '/storage/logs/laravel.log';
    
    if (!file_exists($logPath)) {
        if (file_put_contents($logPath, '')) {
            echo "   ✅ Created log file\n";
        } else {
            echo "   ❌ Failed to create log file\n";
        }
    } else {
        echo "   ✅ Log file exists\n";
    }
    
    // Set log file permissions
    if (file_exists($logPath)) {
        chmod($logPath, 0644);
        echo "   ✅ Set log file permissions\n";
    }
    
    echo "\n";

    // Fix 4: Test file writing
    echo "4. Testing file writing...\n";
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

    // Fix 5: Create symbolic link if missing
    echo "5. Checking symbolic link...\n";
    $publicStorage = $projectRoot . '/public/storage';
    
    if (is_link($publicStorage)) {
        echo "   ✅ Symbolic link exists\n";
    } else {
        echo "   ❌ Symbolic link missing\n";
        echo "   💡 Run: php artisan storage:link\n";
    }
    
    echo "\n";

    // Fix 6: Check PHP configuration
    echo "6. Checking PHP configuration...\n";
    echo "   upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
    echo "   post_max_size: " . ini_get('post_max_size') . "\n";
    echo "   max_file_uploads: " . ini_get('max_file_uploads') . "\n";
    echo "   memory_limit: " . ini_get('memory_limit') . "\n";
    
    echo "\n";

    // Fix 7: Provide manual commands
    echo "7. Manual commands to run on server:\n";
    echo "   cd {$projectRoot}\n";
    echo "   sudo chown -R www-data:www-data storage/\n";
    echo "   sudo chown -R www-data:www-data bootstrap/cache/\n";
    echo "   sudo chmod -R 755 storage/\n";
    echo "   sudo chmod -R 755 bootstrap/cache/\n";
    echo "   php artisan storage:link\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan cache:clear\n";
    
    echo "\n🎉 Permission fix completed!\n";
    echo "\n📋 Next steps:\n";
    echo "1. SSH into your server\n";
    echo "2. Navigate to: cd {$projectRoot}\n";
    echo "3. Run the manual commands above\n";
    echo "4. Test the file upload functionality\n";

} catch (Exception $e) {
    echo "❌ Fix failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
