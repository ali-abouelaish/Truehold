<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class TestStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test storage configuration and URL generation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Laravel Storage Configuration...');
        
        // Test 1: Check if storage link exists
        $this->info('1. Checking storage link...');
        if (is_link(public_path('storage'))) {
            $this->info('   ✓ Storage link exists');
            $this->info('   Link points to: ' . readlink(public_path('storage')));
        } else {
            $this->error('   ✗ Storage link does not exist');
            $this->info('   Run: php artisan storage:link');
        }
        
        // Test 2: Check storage directory
        $this->info('2. Checking storage directory...');
        $storagePath = storage_path('app/public');
        if (is_dir($storagePath)) {
            $this->info('   ✓ Storage directory exists: ' . $storagePath);
        } else {
            $this->error('   ✗ Storage directory does not exist');
        }
        
        // Test 3: Check images directory
        $this->info('3. Checking images directory...');
        $imagesPath = storage_path('app/public/images/properties');
        if (is_dir($imagesPath)) {
            $this->info('   ✓ Images directory exists: ' . $imagesPath);
            $files = glob($imagesPath . '/*');
            $this->info('   Files in directory: ' . count($files));
        } else {
            $this->error('   ✗ Images directory does not exist');
        }
        
        // Test 4: Test URL generation
        $this->info('4. Testing URL generation...');
        $testPath = 'images/properties/test.txt';
        $storageUrl = Storage::disk('public')->url($testPath);
        $helperUrl = StorageHelper::getStorageUrl($testPath);
        
        $this->info('   Storage URL: ' . $storageUrl);
        $this->info('   Helper URL: ' . $helperUrl);
        
        // Test 5: Check APP_URL
        $this->info('5. Checking APP_URL...');
        $appUrl = config('app.url');
        $this->info('   APP_URL: ' . $appUrl);
        
        // Test 6: Test file access
        $this->info('6. Testing file access...');
        if (Storage::disk('public')->exists('images/properties/test.txt')) {
            $this->info('   ✓ Test file exists and is accessible');
        } else {
            $this->warn('   ⚠ Test file does not exist (this is normal if no images uploaded yet)');
        }
        
        $this->info('Storage test complete!');
    }
}
