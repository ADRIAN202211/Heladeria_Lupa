# AGENTS.md

Guía para personas y agentes de IA 

## Contexto

- Sitio web de **Heladería Lupa** (Colombia): menú, carrito, pedido por WhatsApp, mesas con código QR y panel del administrador.
- Stack: **PHP 8.1+** sin frameworks ni Composer, **SQLite** (PDO) y HTML/CSS/JS sencillos. Corre en local con el servidor integrado de PHP.
- La especificación funcional está en `docs/manual-usuario.md` (basado en el manual de usuario de la heladería). Si una decisión cambia lo que hace el manual, actualiza el manual en el mismo cambio.
- Ramas: `production` es producción y `staging` es pruebas. Trabajar siempre en una rama propia creada desde `staging` (`feature/...`, `fix/...`). Flujo: rama propia → PR a `staging` → probar → PR de `staging` a `production`. No hacer push ni PR sin que la persona lo pida.

## Estructura

| Carpeta | Contenido |
|---|---|
| `public/` | Único punto de entrada (`index.php`) y recursos (`assets/`, `uploads/`). Es lo único que debe ser accesible desde la web. |
| `src/` | Lógica: `db.php` (esquema y ajustes), `tienda.php` (productos, categorías, mesas), `carrito.php`, `pedidos.php` (validación y mensaje de WhatsApp), `ctrl_*.php` (controladores), `rutas.php` (tabla de rutas). |
| `views/` | Plantillas PHP. `layouts/` (cabecera y pie), `publico/` (cliente), `admin/` (panel), `parciales/` (piezas reutilizables). |
| `config/config.php` | Valores iniciales y datos por defecto. Los datos reales los cambia el negocio desde el panel. |
| `data/` | Base SQLite local. **No se versiona.** |
| `bin/` | `setup.php` (base y contraseña), `iniciar.sh` (servidor local), `verificar.sh` (sintaxis y pruebas). |
| `tests/` | Pruebas sin dependencias externas (`php tests/run.php`). |
| `docs/` | Manual de usuario y documentación funcional. |

## Reglas de código (obligatorio)

- Cada archivo PHP empieza con `declare(strict_types=1);`.
- Toda salida a HTML pasa por `e()`. Nunca imprimir texto del usuario sin escapar.
- Todo formulario `POST` incluye `csrf_campo()` y el controlador llama a `csrf_verificar()` antes de tocar datos.
- Las rutas que cambian datos del panel llaman a `requerir_admin()`.
- Consultas siempre con parámetros (`?`), nunca concatenando texto del usuario en SQL.
- Dinero en **pesos enteros** (`int`). Mostrarlo solo con `dinero()`. No usar decimales ni `float` para precios.
- Los precios del carrito se leen de la base en cada petición: nunca confiar en un precio que llegue desde el navegador.
- Las imágenes subidas se validan con `getimagesize()` (JPG, PNG o WEBP, máximo 2 MB). No confiar en la extensión del nombre.
- Las rutas nuevas se agregan en `src/rutas.php`, no con condiciones sueltas en `public/index.php`.
- No agregar dependencias de Composer o de npm sin acordarlo con el equipo. La librería de QR (`public/assets/vendor/qrcode.min.js`) va incluida y se documenta en su `LICENSE-qrcode.txt`.

## Pruebas (obligatorio)

1. Antes de cada commit: `bin/verificar.sh` debe terminar con `TODO OK`.
2. Si el cambio corrige un error, agregar en `tests/run.php` una prueba que falle antes del cambio.
3. Si el cambio toca una página, probarla en el navegador en local (`bin/iniciar.sh`) con el flujo completo: menú → carrito → datos → WhatsApp, y el panel.
4. La contraseña del panel nunca va en el código ni en el repositorio: se genera con `bin/setup.php`.

## Datos y privacidad

- No versionar datos de clientes, pedidos reales, teléfonos, bases SQLite (`data/*.sqlite`), imágenes subidas (`public/uploads/`) ni contraseñas.
- Los datos de ejemplo del menú están en `config/config.php`. Son los que aparecen en el manual; reemplazarlos por el catálogo real desde el panel, no tocando el código.

## Convenciones

- Idioma: español en nombres de funciones, variables, rutas, textos y comentarios, igual que el código existente.
- Commits estilo Conventional Commits: `tipo(area): descripción`. Ejemplo: `fix(carrito): no permitir cantidades mayores a 99`.
- Commits pequeños, enfocados en una sola cosa.
- Documentación: si el cambio altera lo que ve el cliente o el panel, actualizar en el mismo commit `docs/manual-usuario.md` y agregar una línea en la sección **Sin publicar** de `CHANGELOG.md`. El detalle del flujo de Git está en `LEEME.md`, sección 8.
- PR: título en español; descripción corta.
- **Sin marca de IA** en commits ni en PR: no agregar `Co-Authored-By` de asistentes de IA, ni "Generated with …", ni emojis o enlaces de herramientas de IA. El autor es la persona que hace el commit.
