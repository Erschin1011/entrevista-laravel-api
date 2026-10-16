# Laravel MVC + API interview starter

Laravel 12 starter with PHP 8.2+, Eloquent ORM, migrations, PostgreSQL, a Blade page and a JSON CRUD API. Clone it, start PostgreSQL, and start implementing the interview task.

## Requirements

- PHP 8.2+ and Composer
- PostgreSQL 14+ installed and running locally
- PHP's `pdo_pgsql` extension enabled
- Node.js 20+ and npm (only needed to build assets; the starter has no frontend dependency)

## Start locally

```bash
composer install
copy .env.example .env        # Windows PowerShell; on macOS/Linux use cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Before running migrations, start the PostgreSQL service and create a local database and user. For example, in `psql` as a PostgreSQL administrator:

```sql
CREATE USER laravel WITH PASSWORD 'secret';
CREATE DATABASE laravel_api OWNER laravel;
```

The `.env.example` defaults match these values (`127.0.0.1:5432`, database `laravel_api`). Change `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` in `.env` to match your local installation. Open `http://127.0.0.1:8000` after `php artisan serve`. If your `.env` already exists, keep your local settings and do not overwrite it.

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
