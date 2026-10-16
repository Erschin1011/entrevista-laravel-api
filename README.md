# Laravel MVC + API — plantilla para entrevista técnica

Starter pequeño y ejecutable con **Laravel 12**, **PHP 8.2+**, **Eloquent ORM**, **Blade**, **PostgreSQL local** y una API REST CRUD. Incluye pruebas con SQLite en memoria, de modo que la suite no depende de que PostgreSQL esté iniciado.

> La plantilla tiene un ejemplo `Post` (título y contenido) para demostrar el recorrido completo. Úsalo como base: cambia los campos y nombres al dominio que indique el ejercicio.

## Índice

1. [Qué incluye y cómo se conectan las piezas](#qué-incluye-y-cómo-se-conectan-las-piezas)
2. [Requisitos](#requisitos)
3. [Instalación en Windows](#instalación-en-windows)
4. [Instalar PostgreSQL y crear la base](#instalar-postgresql-y-crear-la-base)
5. [Clonar y preparar el proyecto](#clonar-y-preparar-el-proyecto)
6. [Iniciar la aplicación](#iniciar-la-aplicación)
7. [Probar la interfaz MVC](#probar-la-interfaz-mvc)
8. [Probar la API REST](#probar-la-api-rest)
9. [Ejecutar pruebas](#ejecutar-pruebas)
10. [Extender el ejemplo a otro CRUD](#extender-el-ejemplo-a-otro-crud)
11. [Comandos Artisan de uso frecuente](#comandos-artisan-de-uso-frecuente)
12. [Solución de problemas](#solución-de-problemas)

## Qué incluye y cómo se conectan las piezas

El repositorio ofrece:

- `GET /api/posts`: lista posts, paginados de 10 en 10.
- `POST /api/posts`: valida y crea un post.
- `GET /api/posts/{post}`: consulta uno por ID.
- `PUT/PATCH /api/posts/{post}`: reemplaza o edita parcialmente.
- `DELETE /api/posts/{post}`: elimina un post.
- `GET /`: vista Blade sencilla que muestra los registros guardados.
- `PostResource`: define qué campos se exponen como JSON.
- `database/migrations/*create_posts_table.php`: esquema de la tabla.
- PostgreSQL como base local de la aplicación; SQLite en memoria para pruebas.

El flujo normal es:

```text
HTTP request -> routes/api.php -> PostController -> validación
             -> modelo Post (Eloquent) -> PostgreSQL -> PostResource -> JSON response
```

En MVC, la ruta web `/` consulta el modelo y entrega datos a la vista Blade. La API no renderiza HTML: devuelve JSON para que Postman, una app Vue u otro cliente lo consuma.

Archivos principales:

| Archivo | Responsabilidad |
|---|---|
| `routes/api.php` | Declara endpoints REST |
| `routes/web.php` | Declara rutas para páginas Blade |
| `app/Http/Controllers/PostController.php` | Coordina validación, Eloquent y respuesta |
| `app/Models/Post.php` | Representa la tabla `posts` y define `$fillable` |
| `app/Http/Resources/PostResource.php` | Da forma estable al JSON de salida |
| `database/migrations/*create_posts_table.php` | Crea la tabla y sus columnas |
| `resources/views/welcome.blade.php` | Página inicial HTML/MVC |
| `.env` | Configuración local; no se sube a GitHub |

## Requisitos

- Windows 10/11, macOS o Linux.
- PHP **8.2 o superior** y Composer 2.
- PostgreSQL **14 o superior**, ejecutándose localmente.
- Extensiones PHP `pdo_pgsql` para usar PostgreSQL y `pdo_sqlite`/`sqlite3` para pruebas.
- Git.
- Node.js/npm son opcionales: solo se usan para compilar los assets de Vite; el CRUD de ejemplo no necesita un frontend JavaScript.

Comprueba en PowerShell:

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

`php --ini` indica cuál es el `php.ini` activo. En `php -m` deben aparecer `pdo_pgsql` y `pdo_sqlite`. Si tienes XAMPP, comprueba que PowerShell realmente use su PHP con `where.exe php`.

## Instalación en Windows

### PHP

Puedes usar PHP 8.2+ de XAMPP/Laragon o instalar una versión para Windows x64. Con winget:

```powershell
winget search --id PHP.PHP
winget install --id PHP.PHP.8.5 -e
```

En el `php.ini` activo, habilita las extensiones necesarias si están comentadas:

```ini
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_pgsql
extension=pgsql
extension=pdo_sqlite
extension=sqlite3
```

Guarda el archivo, abre una terminal nueva y comprueba:

```powershell
php -m | Select-String 'pdo_pgsql|pgsql|pdo_sqlite|sqlite3'
```

### Composer

Instala [Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe). En el asistente, selecciona el `php.exe` correcto. Abre PowerShell de nuevo:

```powershell
composer --version
composer diagnose
```

Composer descarga las dependencias PHP de `composer.json`; `composer.lock` fija versiones reproducibles en la carpeta `vendor/`.

### PostgreSQL

Descárgalo desde [postgresql.org/download/windows](https://www.postgresql.org/download/windows/) o búscalo con winget:

```powershell
winget search --id PostgreSQL.PostgreSQL
winget install --id PostgreSQL.PostgreSQL.17 -e
```

En el instalador selecciona PostgreSQL Server y Command Line Tools. Anota la contraseña elegida para el usuario administrador `postgres` y conserva el puerto `5432` (salvo que ya esté ocupado). Opcionalmente, instala pgAdmin para administrar el servidor con interfaz gráfica.

Verifica que el servicio PostgreSQL esté iniciado (`services.msc`) y que funcione `psql --version`. Si `psql` no se reconoce, agrega al PATH la carpeta `bin` de la instalación PostgreSQL y abre una terminal nueva.

### Git y herramientas frontend opcionales

Git for Windows puede instalarse desde [git-scm.com](https://git-scm.com/download/win) o con `winget install --id Git.Git -e`. VS Code es opcional.

Si quieres modificar CSS/JS, instala Node.js LTS desde [nodejs.org](https://nodejs.org/) o con `winget install --id OpenJS.NodeJS.LTS -e`. Esta plantilla ya declara Vite/Tailwind 4; dentro del repo ejecuta `npm ci`, `npm run dev` o `npm run build`. No hace falta instalar Tailwind global ni ejecutar `tailwindcss init`.

## Instalar PostgreSQL y crear la base

Inicia PostgreSQL desde sus servicios de Windows y abre una consola como administrador:

```powershell
psql -U postgres -h 127.0.0.1 -p 5432
```

Dentro de `psql`, crea un rol de desarrollo y su base:

```sql
CREATE ROLE laravel WITH LOGIN PASSWORD 'elige-tu-clave-local';
CREATE DATABASE laravel_api OWNER laravel;
\q
```

La contraseña de arriba es solo un ejemplo. Usa una clave local propia. El archivo `.env.example` trae una contraseña placeholder `secret`: **cámbiala en `.env`** para que coincida con la contraseña que configuraste en PostgreSQL. No subas `.env` ni contraseñas reales al repositorio.

Prueba la conexión:

```powershell
psql -U laravel -h 127.0.0.1 -p 5432 -d laravel_api
```

Si abre el prompt de `psql`, la conexión funciona. Escribe `\q` para salir.

## Clonar y preparar el proyecto

Desde la carpeta donde guardarás proyectos:

```powershell
git clone https://github.com/Erschin1011/entrevista-laravel-api.git
cd entrevista-laravel-api
```

### Setup automatizado

En PowerShell, el script verifica PHP, Composer y `pdo_pgsql`; instala paquetes desde `composer.lock`, crea `.env` si falta, genera `APP_KEY` si hace falta, comprueba el puerto de PostgreSQL y aplica migraciones:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup.ps1
```

Si aún no creaste la base/usuario PostgreSQL, prepara las dependencias y `.env` sin migrar:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup.ps1 -SkipMigrations
```

Después corrige `DB_PASSWORD` en `.env`, inicia PostgreSQL y corre `php artisan migrate`.

### Setup manual

```powershell
composer install
Copy-Item .env.example .env
```

En macOS/Linux, usa `cp .env.example .env`. Edita la configuración de base de datos en `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel_api
DB_USERNAME=laravel
DB_PASSWORD=tu_clave_local
```

Prepara y migra:

```powershell
php artisan key:generate
php artisan config:clear
php artisan migrate
php artisan migrate:status
```

Si `.env` ya existe, **no lo reemplaces**; ajusta las variables necesarias. `migrate` crea tablas; `migrate:status` confirma qué migraciones corrieron.

## Iniciar y detener

Desde la raíz del repo, deja PostgreSQL iniciado y ejecuta:

```powershell
php artisan serve
```

Laravel muestra la URL local, normalmente [http://127.0.0.1:8000](http://127.0.0.1:8000). La página `/` es Blade/MVC y muestra una cuenta del total de posts. Para apagar el servidor usa `Ctrl+C`.

Para assets opcionales, abre otra terminal en el mismo proyecto:

```powershell
npm ci
npm run dev
```

Detén Vite también con `Ctrl+C`. Para compilar assets de producción ejecuta `npm run build`.

## Probar la interfaz MVC

1. Abre `http://127.0.0.1:8000` para cargar la vista de bienvenida.
2. Confirma que la página responde incluso cuando todavía no hay posts.
3. Crea un post desde `POST /api/posts` como se muestra abajo.
4. Recarga `/`: Blade consulta Eloquent y muestra el post guardado.
5. Para personalizar esa página, edita `resources/views/welcome.blade.php`.

Laravel envía el HTML renderizado desde Blade; no hay una SPA ni Vue en este repo. Los estilos globales de Laravel/Tailwind provienen de `resources/css/app.css` y Vite.

## Probar la API REST

URL base: `http://127.0.0.1:8000`. Con Postman/Insomnia usa JSON raw. Las peticiones de escritura deben incluir `Content-Type: application/json` y es conveniente añadir `Accept: application/json`.

### Leer todos (paginación de 10)

```powershell
curl.exe -i http://127.0.0.1:8000/api/posts
```

### Agregar

```powershell
curl.exe -i -X POST http://127.0.0.1:8000/api/posts -H "Content-Type: application/json" -H "Accept: application/json" -d '{"title":"Primer post","body":"Contenido de ejemplo"}'
```

Esperado: HTTP `201`. La respuesta tiene forma `{ "data": { "id": ..., "title": ..., "body": ... } }`.

### Leer uno

```powershell
curl.exe -i http://127.0.0.1:8000/api/posts/1 -H "Accept: application/json"
```

### Editar parcialmente

```powershell
curl.exe -i -X PATCH http://127.0.0.1:8000/api/posts/1 -H "Content-Type: application/json" -H "Accept: application/json" -d '{"title":"Título actualizado"}'
```

### Reemplazar los campos editables

```powershell
curl.exe -i -X PUT http://127.0.0.1:8000/api/posts/1 -H "Content-Type: application/json" -H "Accept: application/json" -d '{"title":"Título completo","body":"Contenido completo"}'
```

### Eliminar

```powershell
curl.exe -i -X DELETE http://127.0.0.1:8000/api/posts/1 -H "Accept: application/json"
```

Esperado: HTTP `204` sin cuerpo. Al pedir un ID inexistente obtienes `404`; con título/body ausentes o inválidos, `422` y detalle de validación JSON.

### Contrato REST y flujo del código

`routes/api.php` declara `Route::apiResource('posts', PostController::class)`. Esta instrucción crea cinco rutas:

| Método HTTP | Ruta | Método del controlador | Acción |
|---|---|---|---|
| GET | `/api/posts` | `index()` | Lista paginada |
| POST | `/api/posts` | `store()` | Valida y agrega |
| GET | `/api/posts/{post}` | `show()` | Lee un registro |
| PUT/PATCH | `/api/posts/{post}` | `update()` | Edita el registro |
| DELETE | `/api/posts/{post}` | `destroy()` | Elimina |

`Post $post` usa implicit route model binding: Laravel busca el ID de la URL y responde `404` cuando no existe. `PostResource` controla las claves JSON públicas. El modelo `Post` usa Eloquent y `$fillable` limita la asignación masiva a `title` y `body`.

## Pruebas

Las pruebas automatizadas configuran `DB_CONNECTION=sqlite` y `DB_DATABASE=:memory:` en `phpunit.xml`, por lo que puedes probar la lógica sin servidor PostgreSQL:

```powershell
php artisan test --compact
```

El test `tests/Feature/PostApiTest.php` cubre validación, creación, lectura, edición, borrado y respuesta 404. No ejecuta requests reales por red; usa las pruebas HTTP integradas de Laravel.

## Convertir el starter en otro CRUD

Usa `Post` como patrón y reemplázalo por la entidad que pida la consigna (por ejemplo `Task`, `Customer`, `Book`). Pasos:

### 1. Crear modelo, migración y controlador

Ejemplo para `Category`:

```powershell
php artisan make:model Category -m
php artisan make:controller CategoryController --api
```

O crea modelos/controladores resource desde `php artisan make:model --help`. Si la nueva tabla tiene columnas distintas, edita la migración nueva en `database/migrations/` antes de ejecutarla.

Ejemplo de columnas de una tabla sencilla:

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 120);
    $table->text('description')->nullable();
    $table->timestamps();
});
```

Luego aplica solo las nuevas migraciones pendientes:

```powershell
php artisan migrate
```

### 2. Permitir asignación en el modelo

En `app/Models/Category.php` define únicamente los campos que sí pueden venir del formulario/API:

```php
protected $fillable = ['name', 'description'];
```

No añadas `id` o campos internos que el usuario no debería modificar.

### 3. Añadir validación y métodos CRUD

`PostController` ya es un ejemplo funcional:

- `index`: `Model::query()->latest()->paginate(10)`.
- `store`: `$request->validate(...)`, luego `Model::create($validated)` y HTTP 201.
- `show`: recibe `Post $post`; binding devuelve 404 automáticamente.
- `update`: valida campos `sometimes` y llama `$post->update(...)`.
- `destroy`: elimina y responde HTTP 204.

Para `Category`, adapta las reglas, por ejemplo `name => ['required', 'string', 'max:120']`. Si la validación crece, crea un Form Request con `php artisan make:request StoreCategoryRequest`.

### 4. Registrar sus rutas API

En `routes/api.php`:

```php
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);
```

Inspecciona las rutas creadas:

```powershell
php artisan route:list --path=api/categories
```

### 5. Devolver respuestas API consistentes

Para un CRUD real, crea `CategoryResource` con `php artisan make:resource CategoryResource` y define campos JSON explícitos, igual que `app/Http/Resources/PostResource.php`. Devuelve `CategoryResource::collection(...)` desde el listado y un `CategoryResource` desde `show/store/update`.

### 6. Conectar una interfaz Blade

Una API JSON y una interfaz Blade son dos formas distintas de presentar la misma lógica. Para páginas HTML:

1. Añade rutas en `routes/web.php`.
2. Crea vistas en `resources/views/categories/` que extiendan `layouts.app`.
3. En formularios incluye `@csrf`; para actualizar/eliminar usa `@method('PUT')` o `@method('DELETE')` con formulario POST.
4. El controlador web retorna `view(...)` o redirige; puedes reutilizar el mismo modelo y validaciones.

Para una interfaz Vue, React o móvil, consume `/api/categories` desde JavaScript y conserva los endpoints API.

## Problemas frecuentes

| Error | Revisar |
|---|---|
| `php` no se reconoce | PATH; comprueba `where.exe php` y abre una terminal nueva. |
| `could not find driver` en PostgreSQL | Habilita `extension=pdo_pgsql` en el `php.ini` mostrado por `php --ini`, luego confirma con `php -m`. |
| `connection refused` a `127.0.0.1:5432` | Inicia el servicio PostgreSQL y prueba `Test-NetConnection 127.0.0.1 -Port 5432`. |
| `password authentication failed` | Revisa host, base, usuario y contraseña en `.env`; prueba esas mismas credenciales con `psql`. |
| `database does not exist` | Crea `laravel_api` en PostgreSQL o actualiza `DB_DATABASE`. |
| `relation "posts" does not exist` | Ejecuta `php artisan migrate` y verifica que `DB_*` apunten a la base correcta. |
| HTTP 422 | Lee `errors` en el JSON; `title` y `body` son obligatorios en este ejemplo. |
| HTTP 404 | Comprueba el ID y la ruta mostrada por `php artisan route:list`. |
| Vite no encuentra manifest | Ejecuta `npm ci` y `npm run build`, o deja `npm run dev` ejecutándose. |

Logs: `storage/logs/laravel.log`. Nunca publiques `.env`; solo `.env.example` debe estar en GitHub.
