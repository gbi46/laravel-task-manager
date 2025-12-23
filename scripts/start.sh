#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/.."
cd "$SCRIPT_DIR"

echo "[start] Bringing up containers"
docker compose up -d --build

echo "[start] Containers started. To follow logs run: docker compose logs -f web"
