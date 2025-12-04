## Laravel / PHP application Dockerfile for Render

FROM composer:2.8 AS composer_base

FROM php:8.2-fpm-alpine AS php_base

RUN apk add --no-cache \
    nginx \
    bash \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    linux-headers \
    supervisor \
    nodejs \
    npm

RUN docker-php-ext-configure zip && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql zip intl

WORKDIR /var/www/html

COPY --from=composer_base /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

COPY . .

RUN npm ci && npm run build && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

RUN chown -R www-data:www-data storage bootstrap/cache

RUN mkdir -p /run/nginx

COPY nginx-configs/docker-nginx.conf /etc/nginx/nginx.conf

COPY supervisor-configs/docker-supervisord.conf /etc/supervisord.conf

ENV PORT=8080
EXPOSE 8080

CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]


