# ==============================================================================
# STAGE 1: Build Aset Frontend (Tailwind CSS & JavaScript via Vite)
# ==============================================================================
FROM node:22-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources/ resources/
COPY public/ public/

RUN npm run build

# ==============================================================================
# STAGE 2: Build Dependensi PHP (Composer)
# ==============================================================================
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

# ==============================================================================
# STAGE 3: Production Image (PHP 8.3 FPM Alpine)
# ==============================================================================
FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install dependencies sistem Alpine
RUN apk update && apk add --no-cache \
    freetype \
    libjpeg-turbo \
    libpng \
    libzip \
    icu-libs \
    oniguruma \
    netcat-openbsd \
    tzdata \
    ca-certificates

# Install build dependencies sementara untuk kompilasi ekstensi PHP
RUN apk add --no-cache --virtual .build-deps \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        opcache \
        intl \
    && apk del .build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

# Copy konfigurasi PHP custom dan opcache
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copy kode aplikasi
COPY . /var/www/html

# Copy dependensi vendor dari Stage 2
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy aset frontend yang sudah di-compile dari Stage 1
COPY --from=frontend /app/public/build /var/www/html/public/build

# Setup permission & folder
RUN mkdir -p storage/app/public/profile-photos \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/docker/entrypoint.sh

# Expose port PHP-FPM
EXPOSE 9000

# Set entrypoint & perintah default
ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
CMD ["php-fpm"]

