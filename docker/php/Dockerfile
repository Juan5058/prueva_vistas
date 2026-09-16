FROM php:8.4-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libssl-dev libzip-dev libpng-dev libicu-dev \
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis \
    && docker-php-ext-install zip intl opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

USER www-data
