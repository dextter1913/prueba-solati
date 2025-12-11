# API de Tareas (Laravel + PostgreSQL)

API RESTful para gestionar tareas con autenticación por tokens personales de Sanctum, validaciones con Form Requests y persistencia en PostgreSQL.

## Características
- CRUD completo de tareas (id, título, descripción, estado `pending|completed`) bajo patrón MVC + Repository.
- Validación con Form Requests (webforms) y manejo de respuestas JSON con códigos adecuados.
- Seguridad: autenticación `auth:sanctum`, rate limit (60 req/min por usuario/IP) y aislamiento de datos por usuario.
- Documentación OpenAPI lista para importar (`openapi.yaml`) y UI con Stoplight Elements en `/docs`.
- PostgreSQL como base de datos principal y Laravel Sail para levantar el stack rápidamente.

## Requisitos previos
- PHP 8.2+, Composer.
- Docker + Docker Compose (recomendado usar Laravel Sail) o un servidor PostgreSQL accesible.
- Node.js 20+ (solo si deseas compilar los assets del frontend; no es necesario para la API).

## Configuración local (desarrollo con Sail)
1. Copia el entorno y ajusta variables:
   ```bash
   cp .env.example .env
   # DB_HOST=pgsql, DB_PORT=5432, DB_USERNAME=sail, DB_PASSWORD=password (por defecto Sail)
   ```
2. Instala dependencias y genera la clave:
   ```bash
   composer install
   php artisan key:generate
   ```
3. Levanta los contenedores:
   ```bash
   ./vendor/bin/sail up -d
   ```
4. Ejecuta migraciones (incluye Sanctum + tasks):
   ```bash
   ./vendor/bin/sail artisan migrate
   ```
5. Servir la aplicación:
   ```bash
   ./vendor/bin/sail artisan serve
   ```
   La API queda accesible en `http://localhost/api/v1`.

> Si prefieres correr sin Docker, configura tus credenciales PostgreSQL en `.env`, ejecuta `composer install`, `php artisan key:generate`, `php artisan migrate` y luego `php artisan serve`.

## Uso rápido de la API
- Registra un usuario: `POST /api/v1/auth/register` con `name`, `email`, `password`, `password_confirmation`.
- Inicia sesión: `POST /api/v1/auth/login` y toma el token (`Authorization: Bearer {token}`).
- CRUD de tareas (todas requieren token):
  - `GET /api/v1/tasks` (query opcional `per_page` 1-50).
  - `POST /api/v1/tasks` con `title`, `description?`, `status?`.
  - `GET /api/v1/tasks/{id}`
  - `PUT /api/v1/tasks/{id}` con cualquiera de los campos (`title`, `description`, `status`).
- `DELETE /api/v1/tasks/{id}`
- Estados permitidos: `pending`, `completed`.
- Límite de peticiones: 60 por minuto por usuario/IP.

Documentación completa en `openapi.yaml` (importable en Postman/Insomnia).

## Documentación en el navegador (Stoplight Elements)
- Abre `http://localhost/docs` (o tu dominio) para ver la documentación interactiva basada en Stoplight Elements.
- La especificación se sirve desde `http://localhost/openapi.yaml` y es la misma que `openapi.yaml` en la raíz del proyecto.

## Variables de entorno relevantes
- Base de datos: `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- Sanctum: `SANCTUM_STATEFUL_DOMAINS` (solo si usas cookies/SPA), `SANCTUM_EXPIRATION` para caducidad de tokens (opcional).
- URL pública: `APP_URL` debe apuntar al dominio/puerto expuesto.

## Despliegue a producción
1. Configura `.env` con credenciales reales (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` apuntando al dominio).
2. Instala dependencias sin librerías de desarrollo:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. Ejecuta migraciones en modo seguro:
   ```bash
   php artisan migrate --force
   ```
4. Optimiza cachés:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Asegura un proceso de PHP-FPM/queue supervisor y el acceso a PostgreSQL desde el entorno desplegado.

## Tests
No se ejecutaron pruebas automatizadas en esta sesión. Ejecuta `./vendor/bin/sail artisan test` (o `php artisan test`) tras levantar la base de datos para validar todo el stack.
