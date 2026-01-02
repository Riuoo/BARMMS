#!/bin/bash

# Fix Laravel file permissions on DigitalOcean
# Run this script on your server as root or with sudo

echo "Fixing Laravel file permissions..."

# Navigate to your Laravel application directory
cd /var/www/BARMMS || cd /var/www/BARMMS/BARMMS

# Set ownership to www-data (or your web server user)
chown -R www-data:www-data .

# Set directory permissions (755 for directories)
find . -type d -exec chmod 755 {} \;

# Set file permissions (644 for files)
find . -type f -exec chmod 644 {} \;

# Make storage and bootstrap/cache writable
chmod -R 775 storage bootstrap/cache

# Set ownership of storage and cache directories
chown -R www-data:www-data storage bootstrap/cache

# Make artisan executable
chmod +x artisan

echo "Permissions fixed!"
echo "Storage and cache directories are now writable."

