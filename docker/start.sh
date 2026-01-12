#!/bin/bash
set -e

echo "=== Laravel Startup Script ==="
echo "Current directory: $(pwd)"
echo "PHP Version: $(php -v | head -1)"

# Check storage directories
echo "=== Checking storage directories ==="
ls -la /var/www/storage/
ls -la /var/www/storage/framework/

# Check .env file
echo "=== Checking .env file ==="
if [ -f /var/www/.env ]; then
    echo ".env exists"
    grep -E "^APP_" /var/www/.env || true
else
    echo "ERROR: .env file not found!"
fi

# Check database
echo "=== Checking database ==="
if [ -f /var/www/database/database.sqlite ]; then
    echo "SQLite database exists"
    ls -la /var/www/database/database.sqlite
else
    echo "Creating SQLite database..."
    touch /var/www/database/database.sqlite
    chown www-data:www-data /var/www/database/database.sqlite
fi

# Run migrations
echo "=== Running migrations ==="
cd /var/www
php artisan migrate --force 2>&1 || echo "Migration failed!"

# Seed database
echo "=== Seeding database ==="
php artisan db:seed --force 2>&1 || echo "Seeding failed!"

# Test Laravel
echo "=== Testing Laravel bootstrap ==="
php artisan --version 2>&1 || echo "Laravel bootstrap failed!"

# Clear any cached config
echo "=== Clearing cache ==="
php artisan config:clear 2>&1 || true
php artisan cache:clear 2>&1 || true
php artisan view:clear 2>&1 || true

echo "=== Startup complete ==="
