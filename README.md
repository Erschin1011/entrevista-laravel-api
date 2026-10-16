# Laravel MVC + API interview starter

Laravel 12 starter with PHP 8.2+, Eloquent ORM, migrations, PostgreSQL, a Blade page and a JSON CRUD API. Clone it, start PostgreSQL, and start implementing the interview task.

## Requirements

- PHP 8.2+ and Composer
- PostgreSQL 14+ or Docker Desktop with Docker Compose
- PHP's `pdo_pgsql` extension enabled
- Node.js 20+ and npm (only needed to build assets; the starter has no frontend dependency)

## Start locally

```bash
composer install
copy .env.example .env        # Windows PowerShell; on macOS/Linux use cp .env.example .env
php artisan key:generate
docker compose up -d db       # skip if PostgreSQL is already running locally
php artisan migrate
php artisan serve
```

Open `http://127.0.0.1:8000`. PostgreSQL is configured by default. Docker Compose creates database `laravel_api`, user `laravel`, password `secret`, on port `5432`. If your `.env` already exists, keep your local settings and do not overwrite it. For a local PostgreSQL installation, create a database/user or edit the `DB_*` values in `.env`.

Run the automated tests separately with `php artisan test`; they use in-memory SQLite so PostgreSQL is not required just to run tests.

## Example API

```text
GET     /api/posts
POST    /api/posts       {"title":"First post","body":"Hello"}
GET     /api/posts/1
PATCH   /api/posts/1     {"title":"Updated title"}
DELETE  /api/posts/1
```

Send `Accept: application/json` when testing validation errors. Laravel returns 422 JSON for invalid input.

## Where to code

- `routes/api.php`: REST API routes (`Route::apiResource`)
- `routes/web.php`: browser routes
- `app/Http/Controllers/PostController.php`: request validation and CRUD logic
- `app/Models/Post.php`: Eloquent model and mass-assignable fields
- `database/migrations/*create_posts_table.php`: database schema
- `resources/views/posts.blade.php`: server-rendered MVC view

Useful commands: `php artisan route:list`, `php artisan make:model Comment -mcr`, `php artisan make:request StorePostRequest`, `php artisan migrate:status`, `php artisan test`.

This starter is intentionally small so you can extend it under interview time pressure. Add auth, relationships or a frontend only when the task calls for them. PostgreSQL support requires PHP's `pdo_pgsql`; on Windows enable `extension=pdo_pgsql` in the active `php.ini` and reopen the terminal.
