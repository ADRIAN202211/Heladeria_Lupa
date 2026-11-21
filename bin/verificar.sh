#!/usr/bin/env bash
# Verificación antes de subir cambios: sintaxis de todo el PHP + pruebas.
#   bin/verificar.sh
set -uo pipefail

RAIZ="$(cd "$(dirname "$0")/.." && pwd)"
cd "$RAIZ"
fallo=0

echo "1/2 Sintaxis PHP..."
while IFS= read -r -d '' archivo; do
    if ! php -l "$archivo" >/dev/null 2>&1; then
        php -l "$archivo"
        fallo=1
    fi
done < <(find src views config bin tests public -name '*.php' -print0)
[ "$fallo" -eq 0 ] && echo "   sin errores de sintaxis"

echo "2/2 Pruebas..."
php tests/run.php || fallo=1

if [ "$fallo" -eq 0 ]; then
    echo "TODO OK"
else
    echo "HAY ERRORES: revisa la salida de arriba." >&2
fi
exit "$fallo"
