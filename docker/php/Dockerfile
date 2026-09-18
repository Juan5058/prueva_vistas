FROM php:8.4-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip gosu libssl-dev libzip-dev libpng-dev libicu-dev \
    && pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis \
    && docker-php-ext-install zip intl opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY conf.d/opcache.ini /usr/local/etc/php/conf.d/zz-opcache.ini

WORKDIR /var/www/html

RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && usermod -u 1000 www-data && groupmod -g 1000 www-data

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
