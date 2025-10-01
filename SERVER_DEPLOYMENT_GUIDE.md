# Laravel Storage Deployment Guide

## Problem
Images work on localhost but not on the production server due to storage link and URL configuration issues.

## Solutions

### 1. Environment Configuration
Make sure your `.env` file on the server has the correct `APP_URL`:

```env
APP_URL=https://yourdomain.com
# NOT: APP_URL=http://localhost:8000
```

### 2. Storage Link Setup
Run these commands on your server:

```bash
# Navigate to your Laravel project directory
cd /path/to/your/laravel/project

# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 755 storage/
chmod -R 755 public/storage/

# Clear caches
php artisan config:clear
php artisan cache:clear
```

### 3. Web Server Configuration

#### For Apache (.htaccess)
Add this to your `public/.htaccess` file:

```apache
# Handle storage links
RewriteRule ^storage/(.*)$ storage/app/public/$1 [L]
```

#### For Nginx
Add this to your nginx configuration:

```nginx
location /storage {
    alias /path/to/your/laravel/project/storage/app/public;
    try_files $uri $uri/ =404;
}
```

### 4. File Permissions
Set proper ownership and permissions:

```bash
# Set ownership (adjust user/group as needed)
chown -R www-data:www-data storage/
chown -R www-data:www-data public/storage/

# Set permissions
chmod -R 755 storage/
chmod -R 755 public/storage/
```

### 5. Test the Configuration
After deployment, test these URLs:

1. Visit: `https://yourdomain.com/storage/images/properties/`
2. Check if you can access a specific image: `https://yourdomain.com/storage/images/properties/filename.jpg`

### 6. Troubleshooting

#### If images still don't load:
1. Check if the storage link exists: `ls -la public/storage`
2. Verify file permissions: `ls -la storage/app/public/images/properties/`
3. Check web server error logs
4. Ensure the `APP_URL` in `.env` matches your domain

#### Common Issues:
- **403 Forbidden**: Check file permissions
- **404 Not Found**: Storage link not created or web server misconfiguration
- **Wrong URLs**: Check `APP_URL` in `.env` file

### 7. Alternative: Use Absolute URLs
If the above doesn't work, you can modify the `StorageHelper` to use absolute URLs:

```php
public static function getStorageUrl($path)
{
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        return $path;
    }
    
    // Use absolute URL based on current domain
    $baseUrl = request()->getSchemeAndHttpHost();
    return $baseUrl . '/storage/' . ltrim($path, '/');
}
```

### 8. Deployment Checklist
- [ ] Set correct `APP_URL` in `.env`
- [ ] Run `php artisan storage:link`
- [ ] Set proper file permissions
- [ ] Configure web server for storage access
- [ ] Clear Laravel caches
- [ ] Test image URLs in browser
- [ ] Check web server error logs if issues persist
