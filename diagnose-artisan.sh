#!/bin/bash

# Artisan Command Diagnostic Script
# Helps diagnose why php artisan commands are stuck

echo "=== PHP Artisan Diagnostic Tool ==="
echo ""

# Check if we're in Laravel directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Not in Laravel project directory"
    exit 1
fi

echo "1. Checking PHP version..."
php -v 2>&1 | head -3
echo ""

echo "2. Testing basic PHP execution..."
php -r "echo 'PHP works!\n';" 2>&1
if [ $? -eq 0 ]; then
    echo "✅ PHP is working"
else
    echo "❌ PHP is not working"
fi
echo ""

echo "3. Checking for stuck PHP processes..."
STUCK_PROCESSES=$(ps aux | grep -E "artisan|php" | grep -v grep | wc -l)
if [ $STUCK_PROCESSES -gt 0 ]; then
    echo "⚠️  Found $STUCK_PROCESSES PHP/Artisan processes running:"
    ps aux | grep -E "artisan|php" | grep -v grep | head -5
    echo ""
    read -p "Kill stuck processes? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        pkill -9 -f artisan
        pkill -9 php-fpm 2>/dev/null
        echo "✅ Killed stuck processes"
    fi
else
    echo "✅ No stuck processes found"
fi
echo ""

echo "4. Checking .env file..."
if [ -f ".env" ]; then
    echo "✅ .env file exists"
    ENV_SIZE=$(wc -l < .env)
    echo "   Lines: $ENV_SIZE"
    
    # Check for common issues
    if grep -q "DB_CONNECTION" .env; then
        DB_CONN=$(grep "DB_CONNECTION" .env | cut -d '=' -f2)
        echo "   DB Connection: $DB_CONN"
    fi
else
    echo "❌ .env file not found"
fi
echo ""

echo "5. Checking cache files..."
if [ -d "bootstrap/cache" ]; then
    CACHE_FILES=$(find bootstrap/cache -name "*.php" 2>/dev/null | wc -l)
    echo "   Cache files: $CACHE_FILES"
    if [ $CACHE_FILES -gt 0 ]; then
        echo "   ⚠️  Cache files found - may need clearing"
    fi
else
    echo "   ✅ No cache directory"
fi
echo ""

echo "6. Checking storage permissions..."
if [ -d "storage" ]; then
    if [ -w "storage" ]; then
        echo "✅ Storage is writable"
    else
        echo "❌ Storage is NOT writable"
        echo "   Fix with: chmod -R 775 storage"
    fi
else
    echo "❌ Storage directory not found"
fi
echo ""

echo "7. Testing database connection (if configured)..."
if [ -f ".env" ] && grep -q "DB_CONNECTION" .env; then
    DB_CONN=$(grep "DB_CONNECTION" .env | cut -d '=' -f2 | tr -d ' ')
    if [ "$DB_CONN" = "mysql" ] || [ "$DB_CONN" = "mariadb" ]; then
        echo "   Testing MySQL connection..."
        php artisan db:show 2>&1 | head -5 || echo "   ⚠️  Database connection failed"
    fi
else
    echo "   Skipping (no database configured)"
fi
echo ""

echo "8. Testing simple artisan command with timeout..."
timeout 5 php artisan --version 2>&1
EXIT_CODE=$?
if [ $EXIT_CODE -eq 124 ]; then
    echo "❌ Artisan command timed out (stuck)"
elif [ $EXIT_CODE -eq 0 ]; then
    echo "✅ Artisan command works"
else
    echo "⚠️  Artisan command failed with exit code: $EXIT_CODE"
fi
echo ""

echo "9. Checking PHP memory and time limits..."
php -r "echo 'Memory limit: ' . ini_get('memory_limit') . PHP_EOL;"
php -r "echo 'Max execution time: ' . ini_get('max_execution_time') . PHP_EOL;"
echo ""

echo "10. Checking for autoloader..."
if [ -f "vendor/autoload.php" ]; then
    echo "✅ Autoloader exists"
    php -r "require 'vendor/autoload.php'; echo 'Autoloader works\n';" 2>&1
else
    echo "❌ Autoloader not found - run: composer install"
fi
echo ""

echo "=== Diagnostic Complete ==="
echo ""
echo "Recommended fixes:"
echo "  1. Clear caches: rm -rf bootstrap/cache/*.php storage/framework/cache/*"
echo "  2. Fix permissions: chmod -R 775 storage bootstrap/cache"
echo "  3. Kill stuck processes: pkill -9 -f artisan"
echo "  4. Check database connection"
echo "  5. See FIX_ARTISAN_STUCK.md for detailed solutions"



