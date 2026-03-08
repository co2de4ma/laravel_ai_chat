#!/usr/bin/env bash
set -euo pipefail

composer install --no-interaction
npm install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php -r "if (file_exists('.env') && !preg_match('/^APP_KEY=.+/m', file_get_contents('.env'))) { passthru('php artisan key:generate'); }"
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --force
