# AGENTS.md

## Cursor Cloud specific instructions

This is a Laravel 12.x application (`laravel_ai_chat`) using PHP 8.3, Node.js 22, and SQLite.

### Services

| Service | Command | Port | Notes |
|---|---|---|---|
| Laravel dev server | `php artisan serve --host=0.0.0.0 --port=8000` | 8000 | Main backend + serves Blade views |
| Vite dev server | `npm run dev` | 5173 | Frontend hot-reload for CSS/JS assets |

### Key commands

- **Lint:** `./vendor/bin/pint --test` (check only) or `./vendor/bin/pint` (auto-fix)
- **Tests:** `php artisan test`
- **Build assets:** `npm run build`
- **Artisan Tinker:** `php artisan tinker` (interactive REPL for database/model queries)
- **Migrations:** `php artisan migrate`

### Gotchas

- The database is SQLite at `database/database.sqlite`. If it's missing, run `touch database/database.sqlite && php artisan migrate`.
- PHP 8.3 must be installed from the `ppa:ondrej/php` PPA; the default Ubuntu 24.04 repos do not include it.
- Composer is installed globally at `/usr/local/bin/composer`.
- The Vite dev server root URL (`localhost:5173`) returns 404 — this is normal; it serves assets referenced by the Laravel app via `@vite` directives.
