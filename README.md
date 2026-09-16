# Software de Laravel Documental

Sistema de gestión e inventario TRD en **Laravel 13**, **Livewire 3 + Alpine**, **MongoDB 7** (`mongodb/laravel-mongodb`) y **Redis** (`queue:work`).

## Stack

- PHP 8.4/8.5, Laravel 13, Livewire 3, Alpine (incluido en Livewire)
- MongoDB 7 (campo `is_deleted`, sin borrado físico)
- Redis: sesiones, cache y cola `ImportTrdJob`
- Nginx + PHP-FPM vía `docker-compose`

## Puesta en marcha con Docker

```bash
docker compose up --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan db:seed
```

Abre [http://localhost:8000](http://localhost:8000). El worker `queue` ejecuta `php artisan queue:work redis`.

Sin Docker se requiere la extensión `mongodb`, Redis y `composer install`. PHP portable del workspace: `../.tools/php85`.

## Cuentas de demostración

| Rol | Correo | Contraseña |
| :--- | :--- | :--- |
| Super Admin | `admin@trd.gob` | `admin123` |
| Líder Ambiental | `lider@trd.gob` | `lider123` |
| Aprendiz | `aprendiz@trd.gob` | `aprendiz123` |

## Reglas del pliego cubiertas

- Acciones de tabla: solo iconos + tooltip (`x-icon-action`)
- CSV/Excel TRD: encabezados `codigo_seccion` … `disposicion_final` (CT\|E\|M\|S). Ejemplo CSV en `storage/app/examples/trd_import_ejemplo.csv`
- PDF de documentos: MIME `application/pdf`, nombre UUID, tope **10 MB**, disco `Storage::disk('private')`
- Reportes: `/reportes` y descarga `inventario-trd.pdf` (`reports.download-pdf`)
- Auditoría `DocumentAuditLog` (VIEW / DOWNLOAD / UPDATE)
- Soft delete lógico en todos los modelos: `softDelete()`; `delete()` y `forceDelete()` lanzan `CRITICAL_POLICY_VIOLATION`
- Tests: IP no autorizada, `force_logout`, borrado lógico e importación (`php artisan test`)
- Estilo PSR-12 verificado en CI con Laravel Pint (`vendor/bin/pint --test`)

## Tests

```bash
docker compose exec app php artisan test
```
