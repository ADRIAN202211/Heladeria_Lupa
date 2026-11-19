# Manual de usuario: Plataforma web de Heladería Lupa

Este manual explica cómo usar la plataforma en dos partes. La **Parte A** es para el cliente que pide su helado desde el celular o desde la mesa. La **Parte B** es para el administrador del negocio, que gestiona el menú, el WhatsApp, las mesas y los códigos QR desde el panel.

Versión del manual: 13 de noviembre de 2026. Las capturas del manual original muestran la versión del sitio de la heladería Lupa, usada como ejemplo. En este documento las figuras se describen en texto.

---

## Parte A. Manual del cliente

### A1. Inicio

Al abrir el sitio se ve el nombre de la heladería, el menú de navegación (Inicio, Menú y Carrito), el ícono del carrito con el número de productos y el botón **"Pedir ahora"**, que lleva directamente al menú.

*Figura 1 (original): pantalla de inicio del sitio para el cliente.*

### A2. Explorar el menú

Pulsa **"Menú"** o **"Pedir ahora"**. Cada producto muestra su imagen, la categoría (por ejemplo, CONOS), el nombre, una descripción corta, el precio y un botón de carrito. Algunos productos tienen una etiqueta como **"Favorito"** o **"Nuevo"** para destacarlos. Para volver arriba, usa el botón de flecha que aparece en la parte inferior derecha.

*Figura 2 (original): menú con productos, precios y botón para agregar al carrito.*

Para agregar un producto, pulsa el botón del carrito que está al lado del precio.

### A3. Revisar el carrito

En la página **Carrito** aparece cada producto con su precio, la cantidad y el total de la línea. Puedes cambiar la cantidad con los botones **"-"** y **"+"**, o quitar un producto con la **"X"**.

A la derecha aparece el **"Resumen del pedido"**, con el subtotal, el costo de domicilio (o **"No aplica"** si se recoge en tienda) y el total a pagar.

*Figura 3 (original): carrito con cantidades, subtotal y botón para enviar el pedido por WhatsApp.*

> **Nota de la versión actual:** en el carrito, el costo de domicilio dice "Se define al elegir la entrega" y el botón lleva a **"Continuar con mis datos"**. El envío por WhatsApp se hace desde el formulario del paso siguiente, que muestra el domicilio ya calculado.

### A4. Datos del pedido y forma de entrega

Antes de enviar, completa el formulario **"Datos para tu pedido"**. Los campos marcados con asterisco (*) son obligatorios.

1. Nombre y teléfono o celular.
2. Elige cómo quieres recibirlo:
   - **Domicilio** (tiene un costo adicional).
   - **Recoger en tienda** (gratis).
   - **En mi mesa** (si estás en la heladería).
3. Si eliges domicilio, escribe la dirección y el barrio.
4. Elige el método de pago (Efectivo, Nequi o Transferencia).
5. Escribe una nota si quieres (opcional), por ejemplo "Sin crema".

*Figura 4 (original): formulario de datos del pedido, con forma de entrega y método de pago.*

### A5. Enviar el pedido por WhatsApp

1. Revisa el resumen del pedido y el total.
2. Pulsa **"Enviar pedido por WhatsApp"**.
3. Se abre WhatsApp con el mensaje del pedido ya escrito. Revisa que esté bien y envíalo.
4. El negocio te confirma el pedido y el tiempo de entrega por WhatsApp.

Según la nota del resumen, los pagos se hacen al recibir el pedido o por transferencia.

### A6. Horario y contacto

En la parte inferior del sitio aparecen la dirección, el horario de atención, el número de WhatsApp y el correo del negocio.

*Figura 5 (original): pie de página con dirección, horario y contacto.*

### A7. Problemas comunes del cliente

| Situación | Qué hacer |
|---|---|
| El menú no carga | Revisa tu conexión a internet y recarga la página. |
| WhatsApp no se abre | Revisa que tengas WhatsApp instalado o usa la versión web de WhatsApp. |
| No puedo enviar el pedido | Revisa que estén completos los campos con asterisco (nombre, teléfono y, si es domicilio, dirección y barrio). |
| Estoy en la heladería | Elige "En mi mesa" y escanea el código QR de tu mesa para que el pedido llegue con el número de mesa. |

---

## Parte B. Manual del administrador

El panel del administrador tiene una barra superior con cinco secciones: **Productos**, **Categorías**, **Mesas**, **WhatsApp y mensaje**, y **Negocio y promoción**. A la derecha están los botones **"Ver menú"**, que abre el menú público en otra pestaña, y **"Salir"**, que cierra la sesión.

Para entrar, abre el panel y escribe la contraseña del administrador. La contraseña se genera al preparar el sistema (`php bin/setup.php`) y se puede cambiar con `php bin/setup.php --clave=nueva-clave`.

### B1. Productos

En **"Productos"** aparece la lista completa del menú. El orden de la lista es el mismo orden en que se muestran los productos al cliente.

*Figura 6 (original): panel de productos con precio, categoría, estado y acciones.*

Para cada producto puedes:

- **Crear** un producto con **"Nuevo producto"**. Escribe el nombre, la descripción, la categoría, el precio y sube una imagen (JPG, PNG o WEBP, máximo 2 MB).
- **Cambiar el orden** con las flechas de subir (↑) y bajar (↓).
- **Editarlo** con el botón **"Editar"**. Puedes cambiar el precio, la descripción o la disponibilidad.
- **Marcarlo** como "Disponible" o no disponible con el estado. Un producto no disponible no se vende.
- **Eliminarlo** con el ícono de papelera. Piensa bien antes de borrar, porque no se recupera.

El menú del cliente se actualiza al guardar. No hace falta reimprimir los códigos QR por un cambio de precio.

### B2. Categorías y negocio y promoción

**Categorías** sirve para agrupar los productos en el menú. Se puede crear una categoría nueva y borrar las que no tienen productos. Una categoría con productos no se puede borrar: primero hay que cambiar de categoría esos productos.

**Negocio y promoción** guarda el nombre, la dirección, el horario, el teléfono y el correo, que aparecen en el pie de página del sitio. La parte de **promociones** queda pendiente, como indica el manual original, hasta que el equipo confirme su funcionamiento.

### B3. WhatsApp y mensaje del pedido

En esta sección se configura el número que recibe los pedidos y el mensaje que se envía al cliente.

*Figura 7 (original): configuración del número de WhatsApp, costo del domicilio y mensaje del pedido.*

1. En **"Número de WhatsApp"**, escribe el código de país y el celular sin espacios. Para Colombia, el código es **57** (ejemplo: 573245245615).
2. En **"Costo del domicilio"**, escribe el valor en pesos. Escribe **0** si el domicilio es gratis.
3. Revisa el **"Mensaje que se envía"**. Las palabras entre llaves se reemplazan solas con los datos del pedido:
   - `{negocio}`, `{pedido}`, `{subtotal}`, `{domicilio}`, `{total}`, `{nombre}`, `{telefono}` y `{entrega}` (del manual original).
   - `{direccion}`, `{pago}` y `{nota}` (agregadas en esta versión).
4. Revisa la **"Vista previa"** a la derecha para ver cómo llegará el mensaje.
5. Guarda los cambios.

### B4. Mesas y códigos QR

En **"Mesas"** se crean las mesas del local. Cada mesa tiene su propio código QR, que al escanearlo abre el menú con esa mesa ya seleccionada.

*Figura 8 (original): lista de mesas y códigos QR, uno por mesa, con el enlace para abrirlos.*

- Escribe una mesa por línea en **"Mesas (una por línea)"**. Puedes usar nombres como "Mesa 5", "Barra" o "Terraza 2".
- Para crear varias mesas de una vez, escribe la cantidad en **"Generar rápido"** y pulsa **"Crear Mesa 1 a Mesa N"**. Las mesas que ya existen no se duplican.
- Imprime el QR de cada mesa y pégalo en la mesa, en un lugar visible.
- Si cambias el dominio del sitio al publicarlo en internet, vuelve a generar los QR, porque apuntan a la dirección actual.

### B5. Ver el menú y cerrar sesión

- **"Ver menú"**: abre el menú público para revisar cómo lo ve el cliente.
- **"Salir"**: cierra tu sesión. Hazlo al terminar, sobre todo si usas un computador compartido.

---

## Glosario

| Término | Qué significa |
|---|---|
| Carrito | Lista de productos que el cliente va a pedir, con su total. |
| Domicilio | Entrega en la dirección del cliente, con un costo que el negocio define. |
| QR | Código cuadrado que el celular lee con la cámara para abrir el menú. |
| Mesa | Lugar del local al que se asocia un código QR, para saber de dónde llegó el pedido. |
| Llaves `{ }` | Espacios del mensaje que se llenan automáticamente con los datos del pedido. |

## Preguntas frecuentes

**¿Un cliente necesita cuenta?**
No. Solo llena el formulario del pedido.

**¿Puedo cambiar el número de WhatsApp?**
Sí, en la sección **WhatsApp y mensaje del pedido**.

**¿Qué pasa si un producto se agota?**
Márcalo como no disponible; deja de aparecer para el cliente. Si un cliente lo tenía en el carrito, se quita automáticamente y el sitio le avisa.
