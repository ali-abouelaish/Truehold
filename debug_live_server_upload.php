<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\RentalCode;
use Illuminate\Support\Facades\Storage;

echo "🔍 Live Server Upload Diagnostics\n";
echo "=================================\n\n";

try {
    // Test 1: Check server environment
    echo "1. Server Environment Check...\n";
    echo "   PHP Version: " . PHP_VERSION . "\n";
    echo "   Laravel Version: " . app()->version() . "\n";
    echo "   Environment: " . app()->environment() . "\n";
    echo "   Debug Mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
    echo "   App URL: " . config('app.url') . "\n\n";

    // Test 2: Check file permissions
    echo "2. File Permissions Check...\n";
    $storagePath = storage_path('app/public');
    echo "   Storage Path: {$storagePath}\n";
    
    if (is_dir($storagePath)) {
        echo "   ✅ Storage directory exists\n";
        
        if (is_writable($storagePath)) {
            echo "   ✅ Storage directory is writable\n";
        } else {
            echo "   ❌ Storage directory is NOT writable\n";
            echo "   💡 Fix: chmod 755 {$storagePath}\n";
        }
        
        // Check ownership
        $owner = posix_getpwuid(fileowner($storagePath));
        $group = posix_getgrgid(filegroup($storagePath));
        echo "   Owner: " . ($owner['name'] ?? 'Unknown') . "\n";
        echo "   Group: " . ($group['name'] ?? 'Unknown') . "\n";
    } else {
        echo "   ❌ Storage directory missing\n";
        echo "   💡 Fix: mkdir -p {$storagePath}\n";
    }
    
    // Check rental-codes directory
    $rentalCodesDir = $storagePath . '/rental-codes';
    if (is_dir($rentalCodesDir)) {
        echo "   ✅ Rental codes directory exists\n";
        if (is_writable($rentalCodesDir)) {
            echo "   ✅ Rental codes directory is writable\n";
        } else {
            echo "   ❌ Rental codes directory is NOT writable\n";
            echo "   💡 Fix: chmod 755 {$rentalCodesDir}\n";
        }
    } else {
        echo "   ❌ Rental codes directory missing\n";
        echo "   💡 Creating rental codes directory...\n";
        if (mkdir($rentalCodesDir, 0755, true)) {
            echo "   ✅ Created rental codes directory\n";
        } else {
            echo "   ❌ Failed to create rental codes directory\n";
        }
    }
    
    echo "\n";

    // Test 3: Check symbolic link
    echo "3. Symbolic Link Check...\n";
    $publicStorage = public_path('storage');
    echo "   Public storage path: {$publicStorage}\n";
    
    if (is_link($publicStorage)) {
        echo "   ✅ Storage symbolic link exists\n";
        $target = readlink($publicStorage);
        echo "   🔗 Links to: {$target}\n";
        
        if (is_dir($publicStorage)) {
            echo "   ✅ Public storage is accessible\n";
        } else {
            echo "   ❌ Public storage is not accessible\n";
        }
    } else {
        echo "   ❌ Storage symbolic link missing\n";
        echo "   💡 Fix: php artisan storage:link\n";
    }
    
    echo "\n";

    // Test 4: Check upload limits
    echo "4. PHP Upload Limits...\n";
    echo "   upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
    echo "   post_max_size: " . ini_get('post_max_size') . "\n";
    echo "   max_file_uploads: " . ini_get('max_file_uploads') . "\n";
    echo "   max_execution_time: " . ini_get('max_execution_time') . " seconds\n";
    echo "   memory_limit: " . ini_get('memory_limit') . "\n";
    
    // Check if limits are sufficient
    $uploadMax = ini_get('upload_max_filesize');
    $postMax = ini_get('post_max_size');
    
    if (strpos($uploadMax, 'M') !== false) {
        $uploadMaxMB = (int)$uploadMax;
        if ($uploadMaxMB < 10) {
            echo "   ⚠️  upload_max_filesize is less than 10MB\n";
        }
    }
    
    if (strpos($postMax, 'M') !== false) {
        $postMaxMB = (int)$postMax;
        if ($postMaxMB < 50) {
            echo "   ⚠️  post_max_size is less than 50MB\n";
        }
    }
    
    echo "\n";

    // Test 5: Test file upload simulation
    echo "5. File Upload Simulation...\n";
    $testContent = 'Test file content at ' . now();
    $testPath = 'rental-codes/documents/test-' . time() . '.txt';
    
    try {
        if (Storage::disk('public')->put($testPath, $testContent)) {
            echo "   ✅ Successfully stored test file: {$testPath}\n";
            
            if (Storage::disk('public')->exists($testPath)) {
                echo "   ✅ Test file exists in storage\n";
                
                $retrievedContent = Storage::disk('public')->get($testPath);
                if ($retrievedContent === $testContent) {
                    echo "   ✅ Test file content matches\n";
                } else {
                    echo "   ❌ Test file content mismatch\n";
                }
                
                // Test URL generation
                $url = Storage::disk('public')->url($testPath);
                echo "   📁 File URL: {$url}\n";
                
                // Clean up
                Storage::disk('public')->delete($testPath);
                echo "   🧹 Cleaned up test file\n";
            } else {
                echo "   ❌ Test file not found in storage\n";
            }
        } else {
            echo "   ❌ Failed to store test file\n";
        }
    } catch (Exception $e) {
        echo "   ❌ File storage test failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";

    // Test 6: Check web server configuration
    echo "6. Web Server Configuration...\n";
    $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
    echo "   Server Software: {$serverSoftware}\n";
    
    // Check for common web server issues
    if (strpos($serverSoftware, 'Apache') !== false) {
        echo "   🔍 Apache detected - checking .htaccess\n";
        $htaccessPath = public_path('.htaccess');
        if (file_exists($htaccessPath)) {
            echo "   ✅ .htaccess file exists\n";
        } else {
            echo "   ❌ .htaccess file missing\n";
        }
    } elseif (strpos($serverSoftware, 'nginx') !== false) {
        echo "   🔍 Nginx detected - check nginx configuration\n";
    }
    
    // Check for mod_rewrite (Apache)
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if (in_array('mod_rewrite', $modules)) {
            echo "   ✅ mod_rewrite is enabled\n";
        } else {
            echo "   ❌ mod_rewrite is not enabled\n";
        }
    }
    
    echo "\n";

    // Test 7: Check Laravel logs
    echo "7. Laravel Logs Check...\n";
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logSize = filesize($logFile);
        echo "   Log file size: " . number_format($logSize) . " bytes\n";
        
        if ($logSize > 0) {
            $logContent = file_get_contents($logFile);
            $errorCount = substr_count($logContent, 'ERROR');
            $warningCount = substr_count($logContent, 'WARNING');
            
            echo "   Errors in log: {$errorCount}\n";
            echo "   Warnings in log: {$warningCount}\n";
            
            if ($errorCount > 0) {
                echo "   ⚠️  Check recent errors in the log file\n";
            }
        }
    } else {
        echo "   ❌ Log file not found\n";
    }
    
    echo "\n";

    // Test 8: Check database connection
    echo "8. Database Connection Check...\n";
    try {
        $rentalCode = RentalCode::first();
        if ($rentalCode) {
            echo "   ✅ Database connection working\n";
            echo "   📊 Found rental code: {$rentalCode->rental_code}\n";
        } else {
            echo "   ⚠️  Database connected but no rental codes found\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Live server diagnostics completed!\n";
    echo "\n💡 Common live server issues:\n";
    echo "1. File permissions (storage directory not writable)\n";
    echo "2. Missing symbolic link (storage:link not run)\n";
    echo "3. PHP upload limits too low\n";
    echo "4. Web server configuration issues\n";
    echo "5. Missing .htaccess or nginx config\n";
    echo "6. SELinux or security restrictions\n";
    echo "\n🔧 Quick fixes to try:\n";
    echo "1. chmod -R 755 storage/\n";
    echo "2. php artisan storage:link\n";
    echo "3. Check PHP upload limits in php.ini\n";
    echo "4. Check web server error logs\n";
    echo "5. Verify .htaccess file exists\n";

} catch (Exception $e) {
    echo "❌ Diagnostics failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
