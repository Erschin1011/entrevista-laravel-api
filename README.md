# Plantilla de entrevista: Laravel MVC + API + PostgreSQL

Base pequeña y funcional para resolver un ejercicio técnico con **Laravel 12**, **PHP 8.2+**, **Eloquent ORM**, migraciones, una vista **Blade**, endpoints REST y **PostgreSQL local**. Incluye validación, manejo de errores JSON, pruebas, y un script de instalación para Windows PowerShell.

## Índice

1. [Qué trae](#qué-trae)
2. [Requisitos](#requisitos)
3. [Instalación de herramientas en Windows](#instalación-de-herramientas-en-windows)
4. [Instalación de Laravel y creación de proyectos](#instalación-de-laravel-y-creación-de-proyectos)
5. [Instalación de PostgreSQL local](#instalación-de-postgresql-local)
6. [Clonar e instalar el proyecto](#clonar-e-instalar-el-proyecto)
7. [Iniciar y detener](#iniciar-y-detener)
8. [API CRUD de ejemplo](#api-crud-de-ejemplo)
9. [Comandos cotidianos](#comandos-cotidianos)
10. [PHP Artisan: comandos útiles](#php-artisan-comandos-útiles)
11. [Cómo ampliar el CRUD](#cómo-ampliar-el-crud)
12. [Solución de problemas](#solución-de-problemas)

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

Instala PHP 8.2+ (XAMPP/Laragon o una distribución oficial para Windows x64). Asegúrate de instalar el runtime de Visual C++ requerido por esa distribución. Con winget puedes buscar e instalar PHP 8.5 así:

```powershell
winget search --id PHP.PHP
winget install --id PHP.PHP.8.5 -e
```

Si usas XAMPP/Laragon, también puedes usar el PHP incluido: localiza su carpeta `php` y agrégala al PATH. Si tienes varias versiones, comprueba cuál se ejecuta con `where.exe php` y `php -v`.

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

Descarga y ejecuta el instalador oficial [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe). Selecciona el `php.exe` que instalarás/usarás. Cierra y abre PowerShell y confirma:

```powershell
composer --version
composer diagnose
```

Composer instala las librerías PHP declaradas en `composer.json` y fija las versiones en `composer.lock`. Para esta plantilla no hace falta instalar globalmente `laravel/installer`; `composer install` descarga Laravel y sus dependencias locales en `vendor/`.

### 3. PostgreSQL

Instala PostgreSQL desde [postgresql.org/download/windows](https://www.postgresql.org/download/windows/) o con winget:

```powershell
winget search --id PostgreSQL.PostgreSQL
winget install --id PostgreSQL.PostgreSQL.17 -e
```

También puedes elegir otra versión estable que aparezca en la búsqueda. En el instalador selecciona PostgreSQL Server y Command Line Tools; apunta la contraseña del usuario administrativo `postgres` que configures y el puerto (por defecto `5432`). Puedes instalar pgAdmin para administrar gráficamente las bases.

Verifica que el servicio esté iniciado (en `services.msc`, normalmente `postgresql-x64-<versión>`) y que `psql --version` funcione. Agrega al PATH la carpeta `bin` de PostgreSQL si Windows no encuentra `psql.exe`.

### 4. Git, editor y Node opcional

- Instala Git for Windows para clonar el repo (también: `winget install --id Git.Git -e`).
- VS Code es un editor práctico, pero cualquier editor sirve.
- Node.js LTS/npm solo hacen falta si vas a modificar y compilar `resources/css` o `resources/js`. Instala Node LTS desde [nodejs.org](https://nodejs.org/); npm viene incluido.

También puedes usar `winget install --id OpenJS.NodeJS.LTS -e`.

```powershell
node --version
npm --version
```

Los paquetes de Vite/Tailwind ya están declarados en `package.json`. En este repo, `npm ci` instala versiones bloqueadas, `npm run dev` inicia Vite y `npm run build` compila assets. No instales Tailwind globalmente ni ejecutes `tailwindcss init`: la plantilla usa Tailwind 4 y su plugin Vite.

Si agregas Tailwind a un Laravel limpio, instala `tailwindcss` y `@tailwindcss/vite` con npm, añade el plugin al `vite.config.js` e importa `@import 'tailwindcss';` en `resources/css/app.css`. Esta plantilla ya tiene esa integración lista.

### Instalación de Laravel y creación de proyectos

Para usar **este repositorio**, Composer instala Laravel localmente según `composer.lock`. Para crear otro proyecto desde cero puedes usar Composer:

```powershell
composer create-project laravel/laravel mi-proyecto
cd mi-proyecto
```

O instala una vez el instalador global de Laravel:

```powershell
composer global require laravel/installer
laravel --version
laravel new mi-proyecto
```

Si Windows no reconoce `laravel`, agrega `%APPDATA%\Composer\vendor\bin` al PATH y abre una terminal nueva. `laravel new` pregunta opciones de starter kit, base de datos y autenticación; elige según el ejercicio. Laravel es el framework PHP y Composer administra sus dependencias.

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

## PHP Artisan: comandos útiles

`php artisan` es la CLI que viene con Laravel. Ejecútala desde la raíz del proyecto, donde está el archivo `artisan`.

### Descubrir y ejecutar

```powershell
php artisan list                         # lista todos los comandos disponibles
php artisan help migrate                 # ayuda de un comando
php artisan about                        # resumen de entorno, framework y paquetes
php artisan --version                    # versión de Laravel
php artisan route:list                   # inspeccionar rutas
php artisan route:list --path=api        # filtrar rutas por URL
php artisan route:list --name=posts      # filtrar por nombre de ruta
```

### Generar código

```powershell
php artisan make:model Product -m       # modelo y migración
php artisan make:model Product -mcr     # modelo, migración, controlador resource
php artisan make:controller ProductController --api
php artisan make:controller ProductController --resource
php artisan make:migration add_price_to_products_table
php artisan make:request StoreProductRequest
php artisan make:seeder ProductSeeder
php artisan make:factory ProductFactory --model=Product
php artisan make:test ProductApiTest
php artisan make:test ProductRulesTest --unit
```

Opciones comunes: `--no-interaction` para evitar preguntas, `--force` para reemplazar archivos generados si el comando lo permite. Lee `php artisan make:model --help` o `php artisan help make:controller` para las opciones exactas.

### Base de datos, caché y depuración

```powershell
php artisan migrate                    # aplicar migraciones pendientes
php artisan migrate:status             # ver cuáles corrieron
php artisan migrate:rollback           # revertir último lote
php artisan migrate:rollback --step=1  # revertir un paso
php artisan migrate:fresh              # BORRA todas las tablas; solo base desechable
php artisan db:seed                    # ejecutar DatabaseSeeder
php artisan db:show                    # información de conexión/esquema
php artisan config:show database       # inspeccionar configuración de DB
php artisan config:clear               # limpiar caché de configuración
php artisan cache:clear                # vaciar caché de aplicación
php artisan view:clear                 # limpiar vistas compiladas
php artisan optimize:clear             # limpiar cachés de Laravel
php artisan test --compact             # ejecutar PHPUnit en formato corto
vendor/bin/pint                        # formatear código PHP
```

### Servidores y utilidades locales

```powershell
php artisan serve                       # servidor local, normalmente puerto 8000
php artisan serve --host=127.0.0.1 --port=8080
php artisan tinker                      # consola interactiva de Laravel
php artisan tinker --execute='App\Models\Post::count();'
php artisan storage:link                # enlace public/storage si se usa storage público
php artisan route:clear                 # borrar caché de rutas
```

Para detener un comando de servidor o Tinker: `Ctrl+C` o `exit` en Tinker. Para explorar cualquier otro comando, `php artisan list` y `php artisan help <comando>` son la referencia más directa.

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
