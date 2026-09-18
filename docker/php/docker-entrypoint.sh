#!/bin/sh
set -e
cd /var/www/html

as_www() {
    if [ "$(id -u)" = "0" ]; then
        gosu www-data "$@"
    else
        "$@"
    fi
}

if [ ! -f .env ] && [ -f .env.example ]; then
    as_www cp .env.example .env
fi

if [ "${WAIT_FOR_VENDOR:-0}" = "1" ]; then
    i=0
    while [ ! -f vendor/autoload.php ]; do
        i=$((i + 1))
        if [ "$i" -gt 150 ]; then
            echo "vendor/autoload.php no apareció. ¿Falló composer install en app?"
            exit 1
        fi
        sleep 2
    done
else
    mkdir -p vendor
    if [ ! -f vendor/autoload.php ]; then
        composer install --no-interaction --prefer-dist --optimize-autoloader
    fi
    if [ "$(id -u)" = "0" ]; then
        chown -R www-data:www-data vendor
    fi
fi

# Un APP_KEY vacío en el entorno del contenedor pisa el valor del .env.
if [ -z "${APP_KEY:-}" ]; then
    unset APP_KEY
fi

if [ -f .env ] && ! grep -qE '^APP_KEY=base64:.+' .env; then
    as_www php artisan key:generate --force --no-interaction
fi

as_www php artisan config:clear --no-interaction >/dev/null 2>&1 || true

# php-fpm debe quedar como root (workers = www-data). gosu a www-data rompe error_log en /proc/self/fd/2.
if [ "$(id -u)" = "0" ] && [ "$1" != "php-fpm" ]; then
    exec gosu www-data "$@"
fi

exec "$@"
