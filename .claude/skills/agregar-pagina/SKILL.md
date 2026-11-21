---
name: agregar-pagina
description: Usa esta skill cuando haya que agregar una página nueva al sitio de Heladería Lupa (ruta, controlador, vista, enlace de navegación y prueba) siguiendo las reglas de AGENTS.md.
---

# Agregar una página nueva

## Pasos

1. **Ruta.** Agrega la línea en `src/rutas.php`, en la tabla `$tabla`, por ejemplo `'GET contacto' => 'ctrl_contacto'`. No agregues condiciones sueltas en `public/index.php`.
2. **Controlador.** Crea la función en `src/ctrl_publico.php` (cliente) o `src/ctrl_admin.php` (panel). Usa `render('carpeta/vista', ['titulo' => ...])`.
   - Si la ruta recibe un formulario `POST`, llama primero a `csrf_verificar()`.
   - Si la ruta cambia datos del panel, llama primero a `requerir_admin()`.
   - Las consultas usan `?` y nunca concatenan texto del usuario.
3. **Vista.** Crea el archivo en `views/publico/` o `views/admin/`. Define `$seccion` al inicio para marcar el enlace activo.
   - Escapa toda salida con `e()`.
   - Usa `<?= csrf_campo() ?>` dentro de cada formulario `POST`.
   - Muestra el dinero solo con `dinero()`.
4. **Navegación.** Agrega el enlace en `views/layouts/publico.php` (cabecera y pie). Si es del panel, en `views/layouts/admin.php`.
5. **Prueba.** Agrega una prueba en `tests/run.php` que llame a la función del controlador dentro de `ob_start()` y `ob_get_clean()`, y que compruebe un texto clave de la página.
6. **Documentación.** Actualiza `docs/manual-usuario.md` y agrega una línea en la sección **Sin publicar** de `CHANGELOG.md`.
7. **Verificación.** Ejecuta la skill `verificar-cambio` antes del commit.
