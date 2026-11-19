#!/usr/bin/env bash
# Prepara la base de datos (solo la primera vez) y levanta el sitio en local.
#   bin/iniciar.sh            -> http://127.0.0.1:8000/index.php
#   PUERTO=9000 bin/iniciar.sh
set -euo pipefail

RAIZ="$(cd "$(dirname "$0")/.." && pwd)"
PUERTO="${PUERTO:-8000}"
cd "$RAIZ"

if ! command -v php >/dev/null 2>&1; then
    echo "No encuentro PHP. Instala PHP 8.1 o superior con las extensiones pdo_sqlite y mbstring." >&2
    exit 1
fi

if [ ! -f data/lupa.sqlite ]; then
    echo "Primera vez: preparando la base de datos..."
    php bin/setup.php
    echo
fi

echo "Sitio:  http://127.0.0.1:${PUERTO}/index.php"
echo "Panel:  http://127.0.0.1:${PUERTO}/index.php?r=admin"
echo "Detener con Ctrl+C."
exec php -S "127.0.0.1:${PUERTO}" -t public
