#!/bin/bash

# Laravel Storage Deployment Script
# This script ensures storage links are properly configured on the server

echo "Setting up Laravel storage for production..."

# Navigate to the Laravel project directory
cd /path/to/your/laravel/project

# Create storage directories if they don't exist
mkdir -p storage/app/public/images/properties
mkdir -p public/storage

# Set proper permissions
chmod -R 755 storage/
chmod -R 755 public/storage/

# Create the storage link
php artisan storage:link

# Clear configuration cache
php artisan config:clear
php artisan cache:clear

# Set proper ownership (adjust user/group as needed)
# chown -R www-data:www-data storage/
# chown -R www-data:www-data public/storage/

echo "Storage setup complete!"
echo "Make sure your web server can access the public/storage directory"
