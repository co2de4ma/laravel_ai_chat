#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

composer install --no-interaction --prefer-dist
npm install
bash .cursor/prepare_app.sh
