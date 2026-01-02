#!/bin/bash

# Quick Fix Script for Stuck Artisan Commands
# Run this to automatically fix common issues

echo "=== Quick Fix for Stuck Artisan Commands ==="
echo ""

# Check if in Laravel directory
if [ ! -f "artisan" ]; then
    echo "Error: Not in Laravel project directory"
    exit 1
fi

echo "Step 1: Killing stuck PHP processes..."
pkill -9 -f artisan 2>/dev/null
pkill -9 php-fpm 2>/dev/null
echo "✅ Done"
echo ""

echo "Step 2: Clearing all caches..."
rm -rf bootstrap/cache/*.php 2>/dev/null
rm -rf storage/framework/cache/data/* 2>/dev/null
rm -rf storage/framework/views/* 2>/dev/null
rm -rf storage/framework/sessions/* 2>/dev/null
echo "✅ Done"
echo ""

echo "Step 3: Fixing permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null
echo "✅ Done"
echo ""

echo "Step 4: Regenerating autoloader..."
composer dump-autoload --quiet 2>/dev/null
echo "✅ Done"
echo ""

echo "Step 5: Testing artisan command..."
timeout 10 php artisan --version 2>&1
if [ $? -eq 0 ]; then
    echo ""
    echo "✅ SUCCESS! Artisan is working!"
    echo ""
    echo "You can now run:"
    echo "  php artisan migrate"
    echo "  php artisan config:cache"
    echo "  php artisan route:cache"
else
    echo ""
    echo "⚠️  Artisan still not working"
    echo ""
    echo "Try these additional steps:"
    echo "  1. Check database connection in .env"
    echo "  2. Restart PHP-FPM: sudo systemctl restart php8.2-fpm"
    echo "  3. Check PHP error logs"
    echo "  4. See FIX_ARTISAN_STUCK.md for more solutions"
fi



