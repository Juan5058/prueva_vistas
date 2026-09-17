#!/bin/sh
set -e
cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

php artisan config:clear --no-interaction >/dev/null 2>&1 || true

exec "$@"
