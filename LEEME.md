# Heladería Lupa · Sitio web de pedidos (PHP, local)

Sitio para el cliente (menú, carrito, pedido por WhatsApp, mesas con QR) y panel para el administrador (productos, categorías, mesas, WhatsApp y datos del negocio). Está hecho en PHP con SQLite, así que **no necesita servidor de base de datos ni Composer**.

Las reglas de trabajo para personas y agentes de IA están en `AGENTS.md`. El manual de usuario en Markdown está en `docs/manual-usuario.md`.

## Requisitos

- PHP **8.1 o superior** con las extensiones `pdo_sqlite` y `mbstring` (vienen activas en la mayoría de instalaciones).
- Un navegador web.

Comprobar: `php -v` y `php -m | grep -E "pdo_sqlite|mbstring"`.

## Ejecutar en local

Desde la carpeta del proyecto:

```bash
bin/iniciar.sh
```

La primera vez crea la base de datos (`data/lupa.sqlite`), carga el menú de ejemplo y las mesas, y **muestra la contraseña del panel**. Guárdala.

Luego abre:

| Qué | Dirección |
|---|---|
| Sitio del cliente | http://127.0.0.1:8000/index.php |
| Menú | http://127.0.0.1:8000/index.php?r=menu |
| Panel del administrador | http://127.0.0.1:8000/index.php?r=admin |

Para otro puerto: `PUERTO=9000 bin/iniciar.sh`. Para detener el servidor, `Ctrl+C`.

Si prefieres hacerlo a mano:

```bash
php bin/setup.php                  # crea la base y genera una contraseña
php bin/setup.php --clave=mi-clave # o pon tu propia contraseña (mínimo 6 caracteres)
php -S 127.0.0.1:8000 -t public
```

### Cambiar la contraseña del panel

```bash
php bin/setup.php --clave=nueva-clave
```

### Empezar de cero

```bash
php bin/setup.php --reiniciar      # borra la base y la vuelve a crear
```

Esto borra productos, categorías, mesas y ajustes que hayas cambiado. Los carritos de los clientes están en la sesión del navegador, no en la base, así que no se tocan. Las imágenes subidas en `public/uploads/` tampoco se borran; bórralas a mano si quieres.

## Probar

```bash
bin/verificar.sh
```

Revisa la sintaxis de todos los archivos PHP y corre las pruebas de `tests/run.php`, que usan una base temporal y no tocan tu base real. Debe terminar con `TODO OK`.

## Cómo está organizado

```
public/          index.php (único punto de entrada), assets/ (CSS, JS, QR), uploads/ (fotos de productos)
src/             lógica: base de datos, catálogo, carrito, pedido, controladores y rutas
views/           plantillas: layouts, publico/ (cliente), admin/ (panel), parciales/
config/          valores iniciales (menú de ejemplo, mesas, datos del negocio)
data/            base SQLite local (no se versiona)
bin/             setup.php, iniciar.sh, verificar.sh
tests/           pruebas
docs/            manual de usuario
```

Todas las páginas se abren por `index.php?r=...`. Así funcionan los códigos QR de las mesas sin configurar el servidor.

## Flujo del pedido

1. El cliente elige productos en el menú y los agrega al carrito. El carrito se guarda en su sesión.
2. Llena "Datos para tu pedido": nombre, teléfono, entrega (domicilio, recoger o en su mesa), pago y nota.
3. Al enviar, el sitio arma el mensaje con la plantilla del panel y abre WhatsApp con el número del negocio.
4. No se guarda el pedido en la base: el pedido llega al WhatsApp del negocio. (Si quieres un historial de pedidos, es un cambio pendiente.)

**Mesas:** cada QR apunta a `index.php?mesa=Mesa+5`. Al escanearlo, el sitio recuerda la mesa y el pedido sale con "Entrega: En mi mesa (Mesa 5)".

## Códigos QR

Los QR se generan en el panel, en **Mesas y códigos QR**, con la dirección que estés usando (por ejemplo `http://127.0.0.1:8000/...`). Si cambias el dominio al publicar el sitio, vuelve a generarlos e imprimirlos.

## Lo que falta o queda pendiente

- **Fotos reales de los productos.** Sin foto, el cliente ve un cono de ejemplo. Se suben desde el panel, en cada producto.
- **Productos.** El manual menciona 16 productos. Solo se cargan los 8 que se ven en las capturas (en `config/config.php`). Los demás se crean desde el panel.
- **Promociones.** La sección del panel existe pero está pendiente, igual que indica el manual.
- **Historial de pedidos.** No se guardan en la base; solo llegan por WhatsApp.
- **Publicación en internet.** Está pensado para local. Para publicarlo habría que poner HTTPS, cambiar la contraseña y revisar los permisos de `data/` y `public/uploads/`.

## Notas

- La librería de QR (`qrcode` 1.5.4, licencia MIT) va incluida en `public/assets/vendor/`, con su licencia. El sitio no necesita internet para generar los QR.
- El sitio funciona sin JavaScript. El JS solo agrega el botón de volver arriba, la confirmación antes de borrar y el cálculo del total al elegir domicilio.

## Novedades de esta versión

- **Fotos e ilustraciones de los productos.** Cuatro conos y la canasta de chocolate usan fotos recortadas de las capturas del sitio; las demás son ilustraciones SVG en `public/assets/productos/`.
- **Selector de mesa en el menú.** El cliente elige "Para llevar / domicilio" o su mesa; la elección queda en la sesión.
- **Pedidos guardados.** Cada pedido enviado queda en el panel, en **Pedidos**, con estado (pendiente, confirmado, entregado, cancelado), detalle y descarga en CSV.
- **Buscador y categorías** en el menú.
- **Página Información** con "Sobre nosotros" (editable en Negocio y promoción), cómo pedir y preguntas frecuentes.
- **Botón flotante de WhatsApp** en todas las páginas.
