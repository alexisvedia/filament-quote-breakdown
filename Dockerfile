FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    nginx \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Create .env file and SQLite database before composer install
RUN cp .env.example .env \
    && touch /var/www/database/database.sqlite

# Install dependencies (no-scripts to avoid Laravel initialization during build)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Generate APP_KEY and run Laravel post-install scripts
RUN php artisan key:generate --force \
    && php artisan package:discover --ansi

# Install npm dependencies and build assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 10000

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
