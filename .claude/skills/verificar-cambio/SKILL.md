---
name: verificar-cambio
description: Usa esta skill antes de cada commit de Heladería Lupa para comprobar que el cambio pasa las pruebas, cumple las reglas del proyecto y no deja datos reales en el repositorio.
---

# Verificar un cambio en Heladería Lupa

## Cuándo usarla

Antes de crear cualquier commit, o cuando te pidan revisar que un cambio está listo.

## Pasos

1. Ejecuta `bin/verificar.sh` desde la raíz del proyecto. Debe terminar con `TODO OK`. Si falla, corrige antes de seguir.
2. Si el cambio corrige un error, comprueba que existe una prueba en `tests/run.php` que falla con el código anterior. Si no existe, agrégala primero.
3. Si el cambio toca una página, prueba el flujo en el navegador con `bin/iniciar.sh`: menú → carrito → datos → WhatsApp, y el panel.
4. Revisa `git status` y `git diff`. No deben aparecer archivos de `data/`, `public/uploads/`, `.env` ni contraseñas.
5. Revisa que las salidas en HTML usen `e()`, que los formularios `POST` tengan `csrf_campo()` y que el controlador llame a `csrf_verificar()`.
6. Si el cambio altera lo que ve el cliente o el panel, confirma que `docs/manual-usuario.md` y `CHANGELOG.md` quedaron actualizados.

## Respuesta esperada

Resume en una lista qué se verificó, con el resultado de cada paso. No digas que está listo si algún paso falló.
