#!/bin/bash
# Render Deployment Setup Script
# This script helps with initial setup and migrations on Render

set -e

echo "🚀 Starting BARMMS deployment setup..."

# Check if APP_KEY is set
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY not set. Generating new key..."
    php artisan key:generate --force
fi

# Clear all caches first
echo "🧹 Clearing caches..."
php artisan optimize:clear

# Run database migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Cache configuration for better performance
echo "⚙️  Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions (if needed)
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache || true

echo "✅ Setup complete!"
echo "📝 Application is ready to serve requests."

