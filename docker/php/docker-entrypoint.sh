#!/bin/sh
set -e
cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

# Un APP_KEY vacío en el entorno del contenedor pisa el valor del .env.
if [ -z "${APP_KEY:-}" ]; then
    unset APP_KEY
fi

if [ -f .env ] && ! grep -qE '^APP_KEY=base64:.+' .env; then
    php artisan key:generate --force --no-interaction
fi

php artisan config:clear --no-interaction >/dev/null 2>&1 || true

exec "$@"
