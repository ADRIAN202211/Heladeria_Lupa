# Historial de cambios · Heladería Lupa

Lista de cambios que el cliente o el administrador pueden notar, agrupados por versión. El detalle técnico está en el historial de Git (`git log`). Formato inspirado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/).

Cada cambio que altere lo que ve el cliente o el panel debe agregar una línea en la sección **Sin publicar**. Ver `AGENTS.md`.

---

## Sin publicar

Rama de trabajo: `feature/estetica-y-documentacion` (creada desde `staging`).

### Cambiado

- **Cabecera fija.** El menú superior se queda visible al desplazarse por el menú o el carrito.
- **Títulos unificados.** Todos los títulos usan la misma tipografía con serif de la marca.
- **Tarjetas de producto.** Se elevan levemente y la foto se acerca al pasar el cursor (desactivado si el sistema pide reducir el movimiento).
- **Botón de WhatsApp.** Pasa a la esquina inferior derecha y no tapa los precios del menú. En celular se muestra solo como un círculo con el ícono.
- **Botón "Volver arriba".** Queda encima del botón de WhatsApp para que no se superpongan.
- **Celular.** La cabecera es más compacta; la navegación va en una fila que se desliza horizontalmente y el formulario de mesa ocupa todo el ancho.
- **Teclado.** Todos los enlaces, botones y campos muestran un borde rosa al recibir el foco con Tab.
- **Panel del administrador.** Mismo estilo de tipografía en los títulos.

### Corregido

- **Menú.** La sección de la mesa se abría y nunca se cerraba, y el segundo bloque repetía el título "Menú". Ahora hay dos secciones bien cerradas y el segundo título es "Todos los productos". Se agregó una prueba en `tests/run.php` que falla con el código anterior.
- **Pedidos.** Cambiar el estado de un pedido que ya no existe muestra un error en lugar de decir "Estado actualizado.".

### Documentación

- **LEEME.md reescrito.** Se corrigió la afirmación de que los pedidos no se guardaban: desde la versión inicial cada pedido queda en el panel. Se agregaron las secciones de flujo del pedido, códigos QR, pendientes y **control de versiones (Git)**.
- **Manual de usuario ampliado.** Nueva sección de **Pedidos** en el panel, detalle de cada formulario, solución de problemas para el administrador, glosario y preguntas frecuentes ampliadas, y un apéndice sobre Git.
- **Nuevo `CHANGELOG.md`.**
- **AGENTS.md.** Se agregó la regla de actualizar este archivo con cada cambio visible.

---

## Versión inicial · 19 de noviembre de 2026

Commit `7fda0f0` (`feat(sitio): sitio de pedidos de Heladería Lupa`), rama `production`.

### Agregado

- **Fotos e ilustraciones de los productos.** Cuatro conos y la canasta de chocolate usan fotos recortadas; las demás son ilustraciones SVG en `public/assets/productos/`.
- **Selector de mesa en el menú.** El cliente elige "Para llevar / domicilio" o su mesa; la elección queda en la sesión.
- **Pedidos guardados.** Cada pedido enviado queda en el panel, en **Pedidos**, con estado (pendiente, confirmado, entregado, cancelado), detalle y descarga en CSV.
- **Buscador y categorías** en el menú.
- **Página Información** con "Sobre nosotros" (editable en Negocio y promoción), cómo pedir y preguntas frecuentes.
- **Botón flotante de WhatsApp** en todas las páginas.
- **Panel del administrador** con productos, categorías, mesas y códigos QR, WhatsApp y mensaje del pedido, y datos del negocio.
- **Pruebas** sin dependencias externas (`tests/run.php`) y script de verificación (`bin/verificar.sh`).
