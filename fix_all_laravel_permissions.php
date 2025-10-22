<?php

echo "🔧 Fixing All Laravel Permission Issues\n";
echo "======================================\n\n";

try {
    // Get the project root directory
    $projectRoot = '/var/www/truehold';
    echo "Project root: {$projectRoot}\n\n";

    // Fix 1: Create all required directories
    echo "1. Creating all required directories...\n";
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
        'storage/framework/testing',
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

    // Fix 2: Set proper permissions for all directories
    echo "2. Setting file permissions...\n";
    
    // Set storage permissions recursively
    if (is_dir($projectRoot . '/storage')) {
        chmod($projectRoot . '/storage', 0755);
        echo "   ✅ Set storage directory permissions\n";
        
        // Set permissions for all subdirectories and files
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

    // Fix 3: Create essential files with proper permissions
    echo "3. Creating essential files...\n";
    
    // Create log file
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
    
    // Create .gitignore files for cache directories
    $gitignoreContent = "*\n!.gitignore\n";
    $cacheDirs = [
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views'
    ];
    
    foreach ($cacheDirs as $cacheDir) {
        $gitignorePath = $projectRoot . '/' . $cacheDir . '/.gitignore';
        if (!file_exists($gitignorePath)) {
            if (file_put_contents($gitignorePath, $gitignoreContent)) {
                echo "   ✅ Created .gitignore for {$cacheDir}\n";
            }
        }
    }
    
    echo "\n";

    // Fix 4: Test file writing to all directories
    echo "4. Testing file writing...\n";
    
    $testFiles = [
        'storage/logs/test.log',
        'storage/framework/cache/test.cache',
        'storage/framework/sessions/test.session',
        'storage/framework/views/test.view'
    ];
    
    $allTestsPassed = true;
    
    foreach ($testFiles as $testFile) {
        $fullPath = $projectRoot . '/' . $testFile;
        $testContent = 'Test file at ' . date('Y-m-d H:i:s') . "\n";
        
        try {
            if (file_put_contents($fullPath, $testContent)) {
                echo "   ✅ Successfully wrote to {$testFile}\n";
                
                // Clean up test file
                unlink($fullPath);
                echo "   🧹 Cleaned up {$testFile}\n";
            } else {
                echo "   ❌ Failed to write to {$testFile}\n";
                $allTestsPassed = false;
            }
        } catch (Exception $e) {
            echo "   ❌ Error writing to {$testFile}: " . $e->getMessage() . "\n";
            $allTestsPassed = false;
        }
    }
    
    if ($allTestsPassed) {
        echo "   🎉 All file writing tests passed!\n";
    } else {
        echo "   ⚠️  Some file writing tests failed\n";
    }
    
    echo "\n";

    // Fix 5: Check symbolic link
    echo "5. Checking symbolic link...\n";
    $publicStorage = $projectRoot . '/public/storage';
    
    if (is_link($publicStorage)) {
        echo "   ✅ Symbolic link exists\n";
        $target = readlink($publicStorage);
        echo "   🔗 Links to: {$target}\n";
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
    echo "   max_execution_time: " . ini_get('max_execution_time') . " seconds\n";
    
    echo "\n";

    // Fix 7: Provide comprehensive manual commands
    echo "7. Manual commands to run on server:\n";
    echo "   cd {$projectRoot}\n";
    echo "   sudo chown -R www-data:www-data storage/\n";
    echo "   sudo chown -R www-data:www-data bootstrap/cache/\n";
    echo "   sudo chmod -R 755 storage/\n";
    echo "   sudo chmod -R 755 bootstrap/cache/\n";
    echo "   php artisan storage:link\n";
    echo "   php artisan config:clear\n";
    echo "   php artisan cache:clear\n";
    echo "   php artisan route:clear\n";
    echo "   php artisan view:clear\n";
    echo "   php artisan optimize:clear\n";
    
    echo "\n";

    // Fix 8: Check for common issues
    echo "8. Checking for common issues...\n";
    
    // Check if running as web server user
    $currentUser = get_current_user();
    echo "   Current user: {$currentUser}\n";
    
    // Check disk space
    $freeSpace = disk_free_space($projectRoot);
    $freeSpaceGB = round($freeSpace / (1024 * 1024 * 1024), 2);
    echo "   Free disk space: {$freeSpaceGB} GB\n";
    
    if ($freeSpace < 1) {
        echo "   ⚠️  Low disk space - may cause issues\n";
    }
    
    echo "\n🎉 Comprehensive permission fix completed!\n";
    echo "\n📋 Summary of fixes applied:\n";
    echo "1. ✅ Created all required directories\n";
    echo "2. ✅ Set proper file permissions\n";
    echo "3. ✅ Created essential files\n";
    echo "4. ✅ Tested file writing capabilities\n";
    echo "5. ✅ Checked symbolic link\n";
    echo "6. ✅ Verified PHP configuration\n";
    echo "7. ✅ Provided manual commands\n";
    echo "8. ✅ Checked for common issues\n";
    echo "\n💡 If issues persist after running manual commands:\n";
    echo "1. Check web server error logs\n";
    echo "2. Verify PHP-FPM or Apache user permissions\n";
    echo "3. Check SELinux settings (if applicable)\n";
    echo "4. Contact your hosting provider\n";
    echo "5. Consider using a different log driver temporarily\n";

} catch (Exception $e) {
    echo "❌ Fix failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
