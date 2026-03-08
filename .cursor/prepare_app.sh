#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

php -r "file_exists('.env') || copy('.env.example', '.env');"

if php -r '
$envPath = ".env";
$contents = file_get_contents($envPath);

if ($contents === false) {
    fwrite(STDERR, "Unable to read .env\n");
    exit(1);
}

if (preg_match("/^APP_KEY=base64:[^\r\n]+/m", $contents) === 1) {
    exit(0);
}

exit(10);
'; then
    :
else
    status=$?

    if [ "$status" -eq 10 ]; then
        php artisan key:generate --force --no-interaction
    else
        exit "$status"
    fi
fi

php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --graceful --force --no-interaction
