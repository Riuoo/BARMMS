## Multi-stage Dockerfile for Laravel (BARMMS) on Render

# Stage 1: Build frontend assets with Vite
FROM node:20-alpine AS frontend-build

WORKDIR /app

# Install frontend dependencies
COPY package.json package-lock.json ./
RUN npm install

# Copy only what Vite needs to build
COPY vite.config.js ./
COPY resources ./resources

# Build assets (outputs to public/build by default in Laravel+Vite)
RUN npm run build


# Stage 2: PHP + Apache for Laravel
FROM php:8.2-apache

WORKDIR /var/www/html

# Install system dependencies and PHP extensions commonly required by Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        intl \
        mbstring \
        xml \
        zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy full Laravel application
COPY . .

# Copy built frontend assets from the Node build stage
COPY --from=frontend-build /app/public/build ./public/build

# Install PHP dependencies (no dev, optimized autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Set Apache DocumentRoot to public/
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Ensure correct permissions for storage and cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Render will route traffic to the container's HTTP port (Apache defaults to 80)
EXPOSE 80

CMD ["apache2-foreground"]


