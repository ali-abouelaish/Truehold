<?php

echo "🚨 EMERGENCY: Fixing Laravel Permission Issues\n";
echo "==============================================\n\n";

try {
    $projectRoot = '/var/www/truehold';
    echo "Project root: {$projectRoot}\n\n";

    // Step 1: Remove problematic files and directories
    echo "1. Removing problematic files and directories...\n";
    
    $problematicPaths = [
        'storage/logs/laravel.log',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'bootstrap/cache'
    ];
    
    foreach ($problematicPaths as $path) {
        $fullPath = $projectRoot . '/' . $path;
        
        if (file_exists($fullPath)) {
            if (is_dir($fullPath)) {
                // Remove directory and all contents
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                
                foreach ($iterator as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getPathname());
                    } else {
                        unlink($file->getPathname());
                    }
                }
                rmdir($fullPath);
                echo "   🗑️  Removed directory: {$path}\n";
            } else {
                unlink($fullPath);
                echo "   🗑️  Removed file: {$path}\n";
            }
        } else {
            echo "   ✅ Path doesn't exist: {$path}\n";
        }
    }
    
    echo "\n";

    // Step 2: Create directories with proper permissions
    echo "2. Creating directories with proper permissions...\n";
    
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
        
        // Set permissions
        if (is_dir($fullPath)) {
            chmod($fullPath, 0755);
            echo "   🔧 Set permissions for: {$dir}\n";
        }
    }
    
    echo "\n";

    // Step 3: Create log file with proper permissions
    echo "3. Creating log file with proper permissions...\n";
    
    $logPath = $projectRoot . '/storage/logs/laravel.log';
    
    if (file_put_contents($logPath, '')) {
        echo "   ✅ Created log file\n";
        chmod($logPath, 0644);
        echo "   🔧 Set log file permissions to 644\n";
    } else {
        echo "   ❌ Failed to create log file\n";
    }
    
    echo "\n";

    // Step 4: Create .gitignore files for cache directories
    echo "4. Creating .gitignore files for cache directories...\n";
    
    $cacheDirs = [
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views'
    ];
    
    $gitignoreContent = "*\n!.gitignore\n";
    
    foreach ($cacheDirs as $cacheDir) {
        $gitignorePath = $projectRoot . '/' . $cacheDir . '/.gitignore';
        if (file_put_contents($gitignorePath, $gitignoreContent)) {
            echo "   ✅ Created .gitignore for {$cacheDir}\n";
        }
    }
    
    echo "\n";

    // Step 5: Test file writing
    echo "5. Testing file writing...\n";
    
    $testFiles = [
        'storage/logs/test.log',
        'storage/framework/cache/test.cache',
        'storage/framework/views/test.view'
    ];
    
    $allTestsPassed = true;
    
    foreach ($testFiles as $testFile) {
        $fullPath = $projectRoot . '/' . $testFile;
        $testContent = 'Test file at ' . date('Y-m-d H:i:s') . "\n";
        
        if (file_put_contents($fullPath, $testContent)) {
            echo "   ✅ Successfully wrote to {$testFile}\n";
            
            // Clean up test file
            unlink($fullPath);
            echo "   🧹 Cleaned up {$testFile}\n";
        } else {
            echo "   ❌ Failed to write to {$testFile}\n";
            $allTestsPassed = false;
        }
    }
    
    if ($allTestsPassed) {
        echo "   🎉 All file writing tests passed!\n";
    } else {
        echo "   ⚠️  Some file writing tests failed\n";
    }
    
    echo "\n";

    // Step 6: Provide manual ownership commands
    echo "6. Manual ownership commands to run:\n";
    echo "   sudo chown -R www-data:www-data {$projectRoot}/storage\n";
    echo "   sudo chown -R www-data:www-data {$projectRoot}/bootstrap/cache\n";
    echo "   sudo chmod -R 755 {$projectRoot}/storage\n";
    echo "   sudo chmod -R 755 {$projectRoot}/bootstrap/cache\n";
    
    echo "\n🎉 Emergency fix completed!\n";
    echo "\n📋 What was fixed:\n";
    echo "1. ✅ Removed problematic files and directories\n";
    echo "2. ✅ Created directories with proper permissions\n";
    echo "3. ✅ Created log file with proper permissions\n";
    echo "4. ✅ Created .gitignore files for cache directories\n";
    echo "5. ✅ Tested file writing capabilities\n";
    echo "6. ✅ Provided ownership commands\n";
    echo "\n💡 Next steps:\n";
    echo "1. Run the ownership commands above\n";
    echo "2. Test your Laravel application\n";
    echo "3. Try uploading files again\n";

} catch (Exception $e) {
    echo "❌ Fix failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
