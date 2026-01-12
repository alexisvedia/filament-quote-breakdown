#!/bin/bash
set -e

echo "=== Laravel Startup Script ==="
echo "Current directory: $(pwd)"
echo "Current user: $(whoami)"
echo "PHP Version: $(php -v | head -1)"

# Fix permissions at runtime (in case container runs as different user)
echo "=== Fixing permissions ==="
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database 2>/dev/null || true

# Check storage directories
echo "=== Checking storage directories ==="
ls -la /var/www/storage/
ls -la /var/www/storage/framework/

# Verify write permissions
echo "=== Testing write permissions ==="
touch /var/www/storage/test_write.txt && rm /var/www/storage/test_write.txt && echo "Storage: WRITABLE" || echo "Storage: NOT WRITABLE"
touch /var/www/storage/framework/views/test_write.txt && rm /var/www/storage/framework/views/test_write.txt && echo "Views: WRITABLE" || echo "Views: NOT WRITABLE"
touch /var/www/storage/framework/sessions/test_write.txt && rm /var/www/storage/framework/sessions/test_write.txt && echo "Sessions: WRITABLE" || echo "Sessions: NOT WRITABLE"
touch /var/www/storage/framework/cache/data/test_write.txt && rm /var/www/storage/framework/cache/data/test_write.txt && echo "Cache: WRITABLE" || echo "Cache: NOT WRITABLE"

# Check .env file
echo "=== Checking .env file ==="
if [ -f /var/www/.env ]; then
    echo ".env exists"
    grep -E "^APP_" /var/www/.env || true
    grep -E "^SESSION_" /var/www/.env || true
    grep -E "^CACHE_" /var/www/.env || true
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
fi
chmod 666 /var/www/database/database.sqlite 2>/dev/null || true

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

# Cache configuration for production
echo "=== Caching configuration ==="
php artisan config:cache 2>&1 || echo "Config cache failed!"
# Note: route:cache doesn't work with closure-based routes
php artisan view:cache 2>&1 || echo "View cache failed!"

# Test Filament specifically
echo "=== Testing Filament ==="
php artisan about 2>&1 | grep -i filament || echo "Filament info not found"

echo "=== Startup complete ==="
