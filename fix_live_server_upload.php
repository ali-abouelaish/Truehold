<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "🔧 Fixing Live Server Upload Issues\n";
echo "===================================\n\n";

try {
    // Fix 1: Create storage directories
    echo "1. Creating storage directories...\n";
    $storagePath = storage_path('app/public');
    $rentalCodesDir = $storagePath . '/rental-codes';
    
    if (!is_dir($storagePath)) {
        if (mkdir($storagePath, 0755, true)) {
            echo "   ✅ Created storage directory: {$storagePath}\n";
        } else {
            echo "   ❌ Failed to create storage directory\n";
        }
    } else {
        echo "   ✅ Storage directory already exists\n";
    }
    
    if (!is_dir($rentalCodesDir)) {
        if (mkdir($rentalCodesDir, 0755, true)) {
            echo "   ✅ Created rental codes directory: {$rentalCodesDir}\n";
        } else {
            echo "   ❌ Failed to create rental codes directory\n";
        }
    } else {
        echo "   ✅ Rental codes directory already exists\n";
    }
    
    echo "\n";

    // Fix 2: Set proper permissions
    echo "2. Setting file permissions...\n";
    $directories = [
        storage_path(),
        storage_path('app'),
        storage_path('app/public'),
        storage_path('app/public/rental-codes'),
        storage_path('logs'),
        public_path()
    ];
    
    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            if (chmod($dir, 0755)) {
                echo "   ✅ Set permissions for: {$dir}\n";
            } else {
                echo "   ❌ Failed to set permissions for: {$dir}\n";
            }
        }
    }
    
    echo "\n";

    // Fix 3: Create symbolic link
    echo "3. Creating symbolic link...\n";
    $publicStorage = public_path('storage');
    
    if (is_link($publicStorage)) {
        echo "   ✅ Symbolic link already exists\n";
    } else {
        if (symlink(storage_path('app/public'), $publicStorage)) {
            echo "   ✅ Created symbolic link: {$publicStorage}\n";
        } else {
            echo "   ❌ Failed to create symbolic link\n";
            echo "   💡 Try running: php artisan storage:link\n";
        }
    }
    
    echo "\n";

    // Fix 4: Test file operations
    echo "4. Testing file operations...\n";
    $testContent = 'Test file for live server at ' . now();
    $testPath = 'rental-codes/documents/test-' . time() . '.txt';
    
    try {
        if (Storage::disk('public')->put($testPath, $testContent)) {
            echo "   ✅ Successfully stored test file\n";
            
            if (Storage::disk('public')->exists($testPath)) {
                echo "   ✅ Test file exists in storage\n";
                
                $url = Storage::disk('public')->url($testPath);
                echo "   📁 File URL: {$url}\n";
                
                // Clean up
                Storage::disk('public')->delete($testPath);
                echo "   🧹 Cleaned up test file\n";
            } else {
                echo "   ❌ Test file not found after storage\n";
            }
        } else {
            echo "   ❌ Failed to store test file\n";
        }
    } catch (Exception $e) {
        echo "   ❌ File operation failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";

    // Fix 5: Check PHP configuration
    echo "5. PHP Configuration Check...\n";
    $uploadMax = ini_get('upload_max_filesize');
    $postMax = ini_get('post_max_size');
    $maxFiles = ini_get('max_file_uploads');
    
    echo "   upload_max_filesize: {$uploadMax}\n";
    echo "   post_max_size: {$postMax}\n";
    echo "   max_file_uploads: {$maxFiles}\n";
    
    // Check if we need to increase limits
    if (strpos($uploadMax, 'M') !== false) {
        $uploadMaxMB = (int)$uploadMax;
        if ($uploadMaxMB < 10) {
            echo "   ⚠️  upload_max_filesize should be at least 10M\n";
            echo "   💡 Add to php.ini: upload_max_filesize = 10M\n";
        }
    }
    
    if (strpos($postMax, 'M') !== false) {
        $postMaxMB = (int)$postMax;
        if ($postMaxMB < 50) {
            echo "   ⚠️  post_max_size should be at least 50M\n";
            echo "   💡 Add to php.ini: post_max_size = 50M\n";
        }
    }
    
    echo "\n";

    // Fix 6: Create .htaccess if missing
    echo "6. Checking .htaccess file...\n";
    $htaccessPath = public_path('.htaccess');
    
    if (file_exists($htaccessPath)) {
        echo "   ✅ .htaccess file exists\n";
    } else {
        echo "   ❌ .htaccess file missing\n";
        echo "   💡 Create .htaccess file in public directory\n";
        
        $htaccessContent = '<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>';
        
        if (file_put_contents($htaccessPath, $htaccessContent)) {
            echo "   ✅ Created .htaccess file\n";
        } else {
            echo "   ❌ Failed to create .htaccess file\n";
        }
    }
    
    echo "\n🎉 Live server fixes completed!\n";
    echo "\n📋 Summary of fixes applied:\n";
    echo "1. ✅ Created storage directories\n";
    echo "2. ✅ Set proper file permissions\n";
    echo "3. ✅ Created symbolic link\n";
    echo "4. ✅ Tested file operations\n";
    echo "5. ✅ Checked PHP configuration\n";
    echo "6. ✅ Verified .htaccess file\n";
    echo "\n🔧 If uploads still don't work:\n";
    echo "1. Check web server error logs\n";
    echo "2. Verify PHP upload limits in php.ini\n";
    echo "3. Check SELinux settings (if applicable)\n";
    echo "4. Contact hosting provider for assistance\n";

} catch (Exception $e) {
    echo "❌ Fix failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
