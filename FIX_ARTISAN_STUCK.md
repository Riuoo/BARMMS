# Fix All PHP Artisan Commands Stuck

If ALL `php artisan` commands are stuck/freezing, try these solutions:

## Quick Diagnosis

### 1. Check if PHP is Working
```bash
php -v
php -m  # List loaded extensions
```

### 2. Test Simple Artisan Command
```bash
php artisan --version
```

### 3. Check for Hanging Processes
```bash
# Linux/Mac
ps aux | grep artisan
ps aux | grep php

# Kill stuck processes
pkill -f artisan
```

## Common Causes & Solutions

### Issue 1: Database Connection Blocking

**Symptom:** Commands hang when trying to connect to database

**Solution:**
```bash
# Check database connection in .env
cat .env | grep DB_

# Test database connection separately
mysql -u your_username -p -h your_host your_database

# Temporarily disable database in config
# Comment out database connections in config/database.php
```

### Issue 2: Corrupted Cache Files

**Solution:**
```bash
# Clear all caches
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/*
rm -rf storage/framework/views/*
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Issue 3: File Permission Issues

**Solution:**
```bash
# Fix permissions
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Issue 4: PHP Memory/Timeout Issues

**Solution:**
```bash
# Run with increased limits
php -d memory_limit=512M -d max_execution_time=300 artisan --version

# Or create php.ini override
echo "memory_limit = 512M" > php.ini
echo "max_execution_time = 300" >> php.ini
php -c php.ini artisan --version
```

### Issue 5: Environment File Issues

**Solution:**
```bash
# Check .env file syntax
cat .env

# Verify .env exists
ls -la .env

# Test with minimal .env
cp .env .env.backup
# Create minimal .env with just APP_KEY
```

### Issue 6: Autoloader Issues

**Solution:**
```bash
# Regenerate autoloader
composer dump-autoload

# Clear opcache if enabled
php -r "opcache_reset();"
```

### Issue 7: Service Provider Hanging

**Solution:**
```bash
# Check app/Providers files
# Temporarily comment out custom service providers in config/app.php
```

## Step-by-Step Recovery

### Step 1: Kill All PHP Processes
```bash
# Find and kill stuck processes
ps aux | grep php | awk '{print $2}' | xargs kill -9
pkill -9 php
pkill -9 artisan
```

### Step 2: Clear All Caches
```bash
# Remove cache files manually
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf storage/logs/*.log
```

### Step 3: Test Basic PHP
```bash
php -r "echo 'PHP works';"
```

### Step 4: Test Artisan Without Database
```bash
# Temporarily rename .env
mv .env .env.backup
touch .env
echo "APP_NAME=BARMMS" > .env
echo "APP_ENV=local" >> .env
echo "APP_KEY=" >> .env

# Try artisan
php artisan --version
```

### Step 5: Check Database Connection
```bash
# If using MySQL, check if it's running
sudo systemctl status mysql
# or
sudo service mysql status

# Test connection
mysql -u username -p -h host database_name
```

### Step 6: Check Logs
```bash
# Check PHP error log
tail -f /var/log/php-fpm/error.log
# or
tail -f /var/log/php/error.log

# Check Laravel logs
tail -f storage/logs/laravel.log
```

## For DigitalOcean/Production

### Check PHP-FPM Status
```bash
sudo systemctl status php8.2-fpm
# or
sudo service php-fpm status

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### Check Nginx/PHP-FPM Connection
```bash
# Test PHP-FPM socket
ls -la /var/run/php/php8.2-fpm.sock

# Check PHP-FPM pool
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
# Look for: listen = /var/run/php/php8.2-fpm.sock
```

### Increase PHP-FPM Timeout
```bash
sudo nano /etc/php/8.2/fpm/php.ini
# Set:
max_execution_time = 300
memory_limit = 512M

sudo systemctl restart php8.2-fpm
```

## Alternative: Use PHP CLI Directly

If artisan is completely broken:

```bash
# Run commands via tinker
php artisan tinker
# Then run commands manually

# Or use raw PHP
php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php';"
```

## Nuclear Option: Reinstall

If nothing works:

```bash
# Backup
cp -r . ../BARMMS_backup

# Fresh install
composer install --no-dev
php artisan key:generate
php artisan config:cache
```

## Debug Mode

Run with verbose output:

```bash
php artisan --version -vvv
php artisan migrate -vvv
```

## Check System Resources

```bash
# Check disk space
df -h

# Check memory
free -h

# Check CPU
top
```

## Windows-Specific (XAMPP)

If on Windows/XAMPP:

```bash
# Check if MySQL is running
# Open XAMPP Control Panel

# Check PHP path
where php

# Use full path
C:\xampp\php\php.exe artisan --version

# Check php.ini
C:\xampp\php\php.ini
# Set:
memory_limit = 512M
max_execution_time = 300
```



