<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get the public URL for a storage file
     * This method handles both local and production environments
     */
    public static function getStorageUrl($path)
    {
        // If the path is already a full URL, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        
        // For production, use the APP_URL from environment
        if (app()->environment('production')) {
            return config('app.url') . '/storage/' . ltrim($path, '/');
        }
        
        // For local development, use the storage disk URL
        return Storage::disk('public')->url($path);
    }
    
    /**
     * Get the storage URL for property images
     */
    public static function getPropertyImageUrl($filename)
    {
        $path = 'images/properties/' . $filename;
        return self::getStorageUrl($path);
    }
    
    /**
     * Check if a storage file exists
     */
    public static function storageFileExists($path)
    {
        return Storage::disk('public')->exists($path);
    }
}
