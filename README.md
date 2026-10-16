# Plantilla de entrevista: Laravel MVC + API + PostgreSQL

Base pequeña y funcional para resolver un ejercicio técnico con **Laravel 12**, **PHP 8.2+**, **Eloquent ORM**, migraciones, una vista **Blade**, endpoints REST y **PostgreSQL local**. Incluye validación, manejo de errores JSON, pruebas, y un script de instalación para Windows PowerShell.

## Índice

1. [Qué trae](#qué-trae)
2. [Requisitos](#requisitos)
3. [Instalación de herramientas en Windows](#instalación-de-herramientas-en-windows)
4. [Instalación de PostgreSQL local](#instalación-de-postgresql-local)
5. [Clonar e instalar el proyecto](#clonar-e-instalar-el-proyecto)
6. [Iniciar y detener](#iniciar-y-detener)
7. [API CRUD de ejemplo](#api-crud-de-ejemplo)
8. [Comandos cotidianos](#comandos-cotidianos)
9. [Cómo ampliar el CRUD](#cómo-ampliar-el-crud)
10. [Solución de problemas](#solución-de-problemas)

## Qué trae

- `GET /api/posts`: listar posts paginados.
- `POST /api/posts`: crear un post validado.
- `GET /api/posts/{post}`: consultar un post (route model binding).
- `PATCH /api/posts/{post}`: actualizar parcialmente.
- `DELETE /api/posts/{post}`: eliminar.
- Errores de validación en JSON con HTTP 422.
- PostgreSQL como conexión predeterminada para el proyecto.
- Vista Blade en `/` que muestra los posts.
- Pruebas automatizadas que usan SQLite en memoria; **no necesitan tener PostgreSQL activo**.

Archivos centrales: `routes/api.php`, `routes/web.php`, `app/Http/Controllers/PostController.php`, `app/Models/Post.php`, `database/migrations/` y `resources/views/`.

## Requisitos

- Windows 10/11, macOS o Linux.
- PHP **8.2 o superior**, con estas extensiones habituales: `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo`, `pdo_pgsql`, `tokenizer`, `xml`, `ctype` y `json`.
- Composer 2.
- PostgreSQL **14 o superior**, instalado como servicio local y escuchando por TCP.
- Cliente `psql` (se instala con PostgreSQL; opcional si se usa pgAdmin).
- Git.
- Node.js 20+ solo si se van a compilar assets; este starter no requiere frontend Node para la funcionalidad CRUD.

Comprueba lo instalado en PowerShell:

```powershell
php -v
php --ini
php -m
composer --version
psql --version
git --version
node --version
npm --version
```

En la salida de `php -m`, confirma que aparezca `pdo_pgsql`. En `php --ini`, mira **Loaded Configuration File** para saber qué `php.ini` editar. Si el comando `php` no se reconoce, agrega la carpeta que contiene `php.exe` al PATH y abre una terminal nueva.

## Instalación de herramientas en Windows

### 1. PHP

Instala PHP 8.2+ (XAMPP/Laragon o una distribución oficial para Windows x64). Asegúrate de instalar el runtime de Visual C++ requerido por esa distribución.

En el `php.ini` cargado, habilita/descomenta las extensiones necesarias, especialmente:

```ini
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_pgsql
extension=pgsql
```

SQLite suele ser necesario para las pruebas:

```ini
extension=pdo_sqlite
extension=sqlite3
```

Guarda `php.ini`, abre una PowerShell nueva y verifica:

```powershell
php -m | Select-String 'pdo_pgsql|pgsql|pdo_sqlite|sqlite3'
```

### 2. Composer

Instala Composer desde [getcomposer.org/download](https://getcomposer.org/download/). Su instalador de Windows te permite seleccionar el `php.exe` que utilizará. Confirma:

```powershell
composer --version
composer diagnose
```

### 3. PostgreSQL

Instala PostgreSQL desde [postgresql.org/download/windows](https://www.postgresql.org/download/windows/). En el asistente selecciona PostgreSQL Server y Command Line Tools; apunta la contraseña del usuario administrativo `postgres` que configures y el puerto (por defecto `5432`). Puedes instalar pgAdmin para administrar gráficamente las bases.

Verifica que el servicio esté iniciado (en `services.msc`, normalmente `postgresql-x64-<versión>`) y que `psql --version` funcione. Agrega al PATH la carpeta `bin` de PostgreSQL si Windows no encuentra `psql.exe`.

### 4. Git, editor y Node opcional

- Instala Git for Windows para clonar el repo.
- VS Code es un editor práctico, pero cualquier editor sirve.
- Node.js LTS/npm solo hacen falta si vas a modificar y compilar `resources/css` o `resources/js`.

## Instalación de PostgreSQL local

La plantilla **no instala PostgreSQL por ti**: usa la instancia local ya instalada. En una terminal PowerShell abre `psql` como administrador (puedes iniciar sesión como `postgres`):

```powershell
psql -U postgres -h 127.0.0.1 -p 5432
```

Dentro del prompt de `psql` crea un usuario y base dedicados al ejercicio:

```sql
CREATE ROLE laravel WITH LOGIN PASSWORD 'elige-una-clave-local';
CREATE DATABASE laravel_api OWNER laravel;
\q
```

Usa una contraseña local que recuerdes; no subas `.env` ni una contraseña real a GitHub. Si ya existen el rol o la base, no los crees de nuevo: utiliza sus credenciales o modifícalas con permisos de administrador.

Prueba la conexión:

```powershell
psql -U laravel -h 127.0.0.1 -p 5432 -d laravel_api
```

Escribe `\q` para salir. Si falla, comprueba el estado del servicio, el puerto, usuario, contraseña y firewall local.

## Clonar e instalar el proyecto

Desde la carpeta donde guardas tus repositorios:

```powershell
git clone https://github.com/Erschin1011/entrevista-laravel-api.git
cd entrevista-laravel-api
```

### Instalación guiada (Windows PowerShell)

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup.ps1
```

El script verifica PHP/Composer/`pdo_pgsql`, instala paquetes bloqueados, crea `.env` solo si falta, genera `APP_KEY` solo si está vacía, prueba el puerto PostgreSQL y aplica migraciones. Si deseas instalar sin migrar todavía:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup.ps1 -SkipMigrations
```

Después crea o modifica `.env` con el editor y ejecuta manualmente `php artisan migrate` cuando PostgreSQL esté listo.

### Instalación manual (Windows, macOS o Linux)

```powershell
composer install
Copy-Item .env.example .env
```

En macOS/Linux usa `cp .env.example .env`. Edita `.env` y configura tu conexión local:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_api
DB_USERNAME=laravel
DB_PASSWORD=tu_clave_local
```

Luego ejecuta:

```powershell
php artisan key:generate
php artisan config:clear
php artisan migrate
php artisan route:list
```

Si ya tenías `.env`, **no lo reemplaces**: revisa o ajusta solo las variables `DB_*`. La base configurada debe existir antes de `migrate`.

## Iniciar y detener

Desde la raíz del proyecto:

```powershell
php artisan serve
```

Abre [http://127.0.0.1:8000](http://127.0.0.1:8000). La página `/` muestra los posts. Para detener el servidor usa `Ctrl+C`.

PostgreSQL también debe seguir ejecutándose durante el uso normal. No es necesario iniciar `npm run dev` para este CRUD; solo para desarrollar assets frontend.

## API CRUD de ejemplo

Base local: `http://127.0.0.1:8000`. Para solicitudes POST/PATCH envía `Content-Type: application/json` y `Accept: application/json`.

### Listar

```powershell
curl.exe http://127.0.0.1:8000/api/posts
```

### Crear

```powershell
curl.exe -X POST http://127.0.0.1:8000/api/posts -H "Content-Type: application/json" -H "Accept: application/json" -d '{"title":"Primer post","body":"Contenido de ejemplo"}'
```

La respuesta exitosa es HTTP **201**.

### Consultar uno

```powershell
curl.exe http://127.0.0.1:8000/api/posts/1
```

### Actualizar parcialmente

```powershell
curl.exe -X PATCH http://127.0.0.1:8000/api/posts/1 -H "Content-Type: application/json" -H "Accept: application/json" -d '{"title":"Título actualizado"}'
```

### Eliminar

```powershell
curl.exe -X DELETE http://127.0.0.1:8000/api/posts/1 -H "Accept: application/json"
```

Si prefieres Postman/Insomnia, crea las mismas solicitudes y usa JSON raw. Validación incorrecta responde **422**, ID inexistente **404**, creación **201**, eliminación **204**.

## Comandos cotidianos

```powershell
php artisan route:list                    # rutas y nombres
php artisan route:list --path=api         # filtrar rutas API
php artisan migrate:status                # estado de migraciones
php artisan migrate                       # aplicar migraciones pendientes
php artisan migrate:rollback              # revertir último lote (cuidado)
php artisan make:model Comment -mcr       # modelo + migración + resource controller
php artisan make:request StorePostRequest # clase de validación
php artisan make:controller PostController --api
php artisan test --compact                # ejecutar pruebas
vendor/bin/pint                            # formatear PHP
php artisan config:clear                   # limpiar config cache
php artisan optimize:clear                 # limpiar caches Laravel
composer install                           # restaurar paquetes desde lock
composer update                            # actualizar versiones; normalmente no durante la entrevista
```

`migrate:fresh` elimina todas las tablas y datos: úsalo solo en una base de desarrollo desechable. Nunca lo ejecutes sobre una base con datos que quieras conservar.

## Cómo ampliar el CRUD

Ejemplo de ciclo:

1. Diseña los campos y crea migración/modelo: `php artisan make:model Comment -mcr`.
2. Define columnas en `database/migrations/...` y aplica `php artisan migrate`.
3. Añade campos permitidos en `$fillable` del modelo.
4. Define reglas de validación en los métodos del controlador o en un Form Request.
5. Usa `Route::apiResource('comments', CommentController::class)` en `routes/api.php`.
6. Prueba GET/POST/PATCH/DELETE y errores 404/422 desde Postman o pruebas Feature.

Si el ejercicio pide una página HTML tradicional, crea vista Blade y ruta web en vez de devolver JSON. La tabla `posts` de este starter tiene `id`, `title`, `body`, `created_at`, `updated_at`.

## Solución de problemas

| Mensaje | Qué revisar |
|---|---|
| `php` no se reconoce | Agrega el directorio de PHP al PATH y abre una terminal nueva. Confirma `where.exe php`. |
| `could not find driver` / `could not find driver (pgsql)` | Habilita `extension=pdo_pgsql` en el `php.ini` indicado por `php --ini`; reabre terminal y verifica `php -m`. |
| `connection refused` a 127.0.0.1:5432 | Inicia PostgreSQL y confirma puerto/host con `Test-NetConnection 127.0.0.1 -Port 5432`. |
| `password authentication failed` | Ajusta `DB_USERNAME`/`DB_PASSWORD` en `.env`; prueba con `psql` usando las mismas credenciales. |
| `database does not exist` | Crea `laravel_api` en PostgreSQL o corrige `DB_DATABASE`. |
| `relation "posts" does not exist` | Ejecuta `php artisan migrate` contra la base correcta y revisa el estado con `migrate:status`. |
| La configuración antigua sigue cargada | Ejecuta `php artisan config:clear`; verifica `.env` y `config:show database.default`. |
| Puerto 5432 ocupado | Mira qué servicio lo ocupa; configura PostgreSQL y `.env` con otro puerto si hace falta. |
| Error 422 | Revisa el JSON de respuesta; `title` y `body` son requeridos y deben ser strings. |

Logs de Laravel: `storage/logs/laravel.log`. No publiques el `.env`; solo se versiona `.env.example`.
