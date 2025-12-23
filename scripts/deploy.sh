#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."
cd "$SCRIPT_DIR"

echo "[deploy] Starting deployment in $SCRIPT_DIR"

command -v docker >/dev/null 2>&1 || { echo "docker not installed" >&2; exit 1; }
command -v docker-compose >/dev/null 2>&1 || true

echo "[deploy] Building and starting containers (db only first)"
docker compose build --pull
docker compose up -d db

echo "[deploy] Waiting for database to be ready (timeout 60s)"
timeout=60
elapsed=0
sleep_interval=2
while true; do
  if docker compose exec -T db mysqladmin ping -h127.0.0.1 -uroot -p"${MYSQL_ROOT_PASSWORD:-secret}" --silent >/dev/null 2>&1; then
    echo "[deploy] Database is up"
    break
  fi
  if [ "$elapsed" -ge "$timeout" ]; then
    echo "[deploy] Database did not become ready in ${timeout}s" >&2
    exit 1
  fi
  sleep "$sleep_interval"
  elapsed=$((elapsed + sleep_interval))
done

echo "[deploy] Installing PHP dependencies (composer)"
docker compose exec -T app composer install --no-interaction --prefer-dist --optimize-autoloader

echo "[deploy] Generating app key"
docker compose exec -T app php artisan key:generate || true

echo "[deploy] Running migrations and seeders"
docker compose exec -T app php artisan migrate --force --seed

echo "[deploy] Installing node dependencies and building assets"
if docker compose exec -T node npm ci --no-audit --no-fund 2>/dev/null; then
  docker compose exec -T node npm run build
else
  docker compose exec -T node npm install --no-audit --no-fund
  docker compose exec -T node npm run build
fi

echo "[deploy] Starting remaining services"
docker compose up -d --no-deps --build web app

echo "[deploy] Deployment complete. Visit http://localhost:8000"
