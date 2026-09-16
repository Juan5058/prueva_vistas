#!/bin/sh
set -e
cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ -f .env ] && ! grep -qE '^APP_KEY=base64:.+' .env; then
    php artisan key:generate --force --no-interaction
fi

exec "$@"
