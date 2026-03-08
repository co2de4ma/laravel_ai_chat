# AGENTS.md

## Cursor Cloud specific instructions

This is a Laravel 12.x application (`laravel_ai_chat`) using PHP 8.3, Node.js 22, and SQLite.

### Services

| Service | Command | Port | Notes |
|---|---|---|---|
| Laravel + queue + logs + Vite | `composer dev` | 8000 / 5173 | Preferred dev entrypoint; starts all required services with cloud-friendly host settings |
| Laravel dev server | `composer run dev:server` | 8000 | Main backend + serves Blade views |
| Vite dev server | `composer run dev:vite` | 5173 | Frontend hot-reload for CSS/JS assets with a browser-reachable HMR URL |

### Key commands

- **Lint:** `./vendor/bin/pint --test` (check only) or `./vendor/bin/pint` (auto-fix)
- **Tests:** `php artisan test`
- **Build assets:** `npm run build`
- **Start full dev stack:** `composer dev`
- **Artisan Tinker:** `php artisan tinker` (interactive REPL for database/model queries)
- **Migrations:** `php artisan migrate`

### Gotchas

- The database is SQLite at `database/database.sqlite`. If it's missing, run `touch database/database.sqlite && php artisan migrate`.
- PHP 8.3 must be installed from the `ppa:ondrej/php` PPA; the default Ubuntu 24.04 repos do not include it.
- Composer is installed globally at `/usr/local/bin/composer`.
- The Vite dev server root URL (`localhost:5173`) returns 404 — this is normal; it serves assets referenced by the Laravel app via `@vite` directives.
- The Vite HMR URL is pinned through `vite.config.js` so Laravel writes `127.0.0.1:5173` to `public/hot` instead of an unusable IPv6 loopback address.
