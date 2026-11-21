# Manual de usuario · Plataforma web de Heladería Lupa

Versión del manual: **20 de noviembre de 2026**. Describe la versión del sitio que está en la rama `staging` y los cambios listados en `CHANGELOG.md`.

Este manual explica cómo usar la plataforma en tres partes:

- **Parte A · Cliente.** Para quien pide su helado desde el celular o desde la mesa.
- **Parte B · Administrador.** Para la persona del negocio que gestiona pedidos, menú, mesas, WhatsApp y datos desde el panel.
- **Parte C · Solución de problemas.** Qué hacer cuando algo no sale como se espera.

Al final hay un **Glosario**, **Preguntas frecuentes** y un **Apéndice** sobre el control de cambios (Git) para quien mantiene el sistema.

> Nota: las pantallas se describen en texto. Las capturas del manual original no están incluidas en el repositorio.

---

## Parte A · Manual del cliente

### A1. Inicio

Al abrir el sitio aparecen:

- **Barra superior** con el nombre de la heladería y el menú de navegación: **Inicio**, **Menú**, **Información** y **Carrito**.
- **Ícono del carrito** con el número de unidades que llevas.
- Botón **"Pedir ahora"**, que lleva directamente al menú.
- Un bloque de **tres pasos**: elige tus favoritos, agrégalos al carrito y completa tus datos, y envía el pedido por WhatsApp.
- **"Lo más pedido"**: los primeros cuatro productos disponibles del menú, en el orden que definió el negocio. Para ver todo, pulsa **"Ver el menú completo"**.

En celular, la barra superior se acomoda en dos filas: el logo y el carrito arriba, y las opciones de navegación debajo. Si no caben, desliza la fila con el dedo.

### A2. Explorar el menú

Entra con **"Menú"** o **"Pedir ahora"**.

**Elegir mesa o forma de entrega.** Lo primero es la caja **"¿Estás en la heladería?"**:

- Deja **"Para llevar / domicilio"** si vas a recoger o te lo llevan.
- Si estás en la heladería, elige tu mesa de la lista y pulsa **"Guardar"**. El sitio te confirma: *"Estás en Mesa 5. Tu pedido llegará a esa mesa."*
- Para cambiar, vuelve a elegir y guarda. Si quieres dejar de estar en mesa, elige **"Para llevar / domicilio"**.

**Buscar y filtrar.**

- Escribe en el buscador por nombre o sabor, por ejemplo *arequipe*, y pulsa **"Buscar"**.
- Usa la lista **"Todas las categorías"** para ver solo una categoría (por ejemplo, Conos o Canastas).
- Cuando hay un filtro activo aparece el botón **"Limpiar"**, que muestra todo el menú otra vez.
- El texto **"N productos"** indica cuántos resultados hay.
- Si no hay coincidencias, el sitio dice: *"No encontramos productos con esa búsqueda."*

**Cada producto muestra:**

- Su foto (o una ilustración si aún no tiene foto).
- Un distintivo opcional: **Favorito** o **Nuevo**.
- La categoría en mayúsculas, el nombre y una descripción corta.
- El precio en pesos.
- Un botón redondo con ícono de carrito, al lado del precio, para agregarlo.

Para agregar un producto, pulsa el botón del carrito de la tarjeta. La página vuelve a mostrar el menú, con el mensaje de confirmación. Si el producto ya no está disponible, el sitio muestra: *"Ese producto no está disponible en este momento."*

Los productos aparecen agrupados por categoría y en el mismo orden que definió el negocio. Los productos sin categoría aparecen bajo el título **OTROS**.

Para volver arriba, usa el botón **↑** que aparece en la esquina inferior derecha al bajar.

### A3. Revisar el carrito

En **Carrito** verás cada producto con su foto, su precio unitario, la cantidad y el total de la línea.

- **"−" y "+"** cambian la cantidad. El máximo por producto es **99 unidades**.
- **"X"** quita el producto.
- **"Vaciar carrito"** quita todo; el sitio pide confirmación antes de hacerlo.

A la derecha aparece el **Resumen del pedido**:

- **Subtotal**: suma de los productos.
- **Domicilio**: se muestra como *"Se define al elegir la entrega"* hasta que escoges la forma de entrega en el siguiente paso. Si eliges domicilio, se suma el costo que definió el negocio; si eliges recoger o mesa, dice *"No aplica"*.
- **Total a pagar**.
- Botón verde **"Continuar con mis datos"**.

Si el carrito está vacío, verás *"Tu carrito está vacío."* y el botón **"Ir al menú"**.

Si un producto se agota mientras lo tienes en el carrito, el sitio lo quita y te avisa: *"Algunos productos ya no están disponibles y se quitaron del carrito."*

Tu carrito se guarda en la sesión de tu navegador: se conserva mientras no cierres el navegador por completo. Si lo cierras, el carrito se vacía.

### A4. Datos del pedido y forma de entrega

Pulsa **"Continuar con mis datos"**. El formulario **"Datos para tu pedido"** tiene los siguientes campos. Los que llevan asterisco (*) son obligatorios.

| Campo | Qué escribir | Reglas |
|---|---|---|
| **Nombre** * | Tu nombre | Al menos 2 letras. Máximo 80 caracteres. |
| **Teléfono** * | Tu celular o teléfono | Al menos 7 dígitos. Puedes usar espacios y el +57. |
| **Forma de entrega** * | Una de las tres opciones (ver abajo) | Obligatoria. |
| **Dirección** | Calle, carrera y número | Solo si eliges domicilio. Al menos 5 caracteres. Máximo 120. |
| **Barrio** | Tu barrio | Solo si eliges domicilio. Al menos 2 caracteres. Máximo 60. |
| **Método de pago** * | Efectivo, Nequi o Transferencia | Obligatorio. |
| **Nota** | Por ejemplo, *"sin crema"* | Opcional. Máximo 300 caracteres. |

**Forma de entrega:**

- **Domicilio.** Aparecen los campos de dirección y barrio, y el total se actualiza al momento con el costo del domicilio.
- **Recoger en tienda.** Sin costo adicional.
- **En mi mesa.** Solo funciona si estás en la heladería y ya escaneaste el código QR de tu mesa o la elegiste en el menú. Si no lo hiciste, el sitio te lo pide: *"Para 'En mi mesa' primero escanea el código QR de tu mesa."*

Si falta algún dato, el formulario vuelve a mostrarse con el mensaje *"Revisa los campos marcados."* y un texto en rojo debajo de cada campo con el problema. Tus datos ya escritos se conservan.

El resumen de la derecha muestra los productos, el subtotal, el domicilio y el total.

### A5. Enviar el pedido por WhatsApp

1. Revisa el resumen del pedido y el total.
2. Pulsa **"Enviar pedido por WhatsApp"**.
3. Se abre WhatsApp con el mensaje ya escrito. Un ejemplo de cómo llega:

   > ¡Hola Heladería Lupa! 🍦 Quiero hacer este pedido:
   > • 2 x Cono Arequipe con Chips — $19.000
   > Subtotal: $19.000 · Domicilio: No aplica · **Total a pagar: $19.000**
   > Nombre, teléfono, entrega, dirección, pago y nota.

4. Revisa que el mensaje esté bien y envíalo.
5. El negocio te confirma el pedido y el tiempo de entrega por WhatsApp.

Tu pedido **queda registrado** en el sistema del negocio aunque WhatsApp no se abra. Si WhatsApp no abre, tu carrito sigue ahí: vuelve a intentarlo.

Según el aviso del sitio, el pago se hace al recibir el pedido o por transferencia.

### A6. Información, horario y contacto

**Información** tiene tres partes:

- **Sobre nosotros**: texto que el negocio escribe en el panel.
- **Cómo pedir**: los pasos resumidos.
- **Preguntas frecuentes**: respuestas desplegables.

El pie de página, en todas las páginas, muestra:

- La **dirección** y el **horario** de atención.
- El **teléfono**, con un enlace **"Escríbenos por WhatsApp"** y, si existe, el **correo**.

El botón verde de WhatsApp (en celular, un círculo con ícono; en computador, un botón con texto) abre el chat con el negocio en cualquier página.

### A7. Problemas comunes del cliente

| Situación | Qué hacer |
|---|---|
| El menú no carga | Revisa tu conexión a internet y recarga la página. |
| Veo el mensaje "Por ahora no hay productos disponibles" | El negocio está actualizando el menú. Vuelve en unos minutos. |
| WhatsApp no se abre | Revisa que tengas WhatsApp instalado o usa WhatsApp Web. El carrito no se pierde. |
| No puedo enviar el pedido | Revisa los campos marcados en rojo (nombre, teléfono, forma de entrega y pago y, si es domicilio, dirección y barrio). |
| Estoy en la heladería | Escanea el QR de tu mesa, o elige tu mesa en el menú y pulsa **Guardar**. Luego elige "En mi mesa". |
| Quiero cambiar cantidades | Usa "−" y "+" en el carrito. Máximo 99 por producto. |
| Un producto desapareció del menú | Puede estar agotado. El negocio lo vuelve a activar cuando esté disponible. |

---

## Parte B · Manual del administrador

### B0. Entrar al panel

Abre el panel en `index.php?r=admin` (en el equipo local: `http://127.0.0.1:8000/index.php?r=admin`). Escribe la contraseña del administrador.

- Si la contraseña es incorrecta, aparece *"Contraseña incorrecta."*
- La contraseña se crea al preparar el sistema con `php bin/setup.php` y se cambia con `php bin/setup.php --clave=nueva-clave` (mínimo 6 caracteres). Ver el apartado B8.

La **barra superior** del panel tiene el logo, el botón **"Ver menú ↗"** (abre el menú del cliente en otra pestaña, para revisar cómo lo ve) y **"Salir"** (cierra tu sesión).

La **barra de secciones** tiene seis pestañas:

| Pestaña | Para qué sirve |
|---|---|
| **Pedidos** | Ver los pedidos recibidos, cambiar su estado y descargar el CSV. |
| **Productos** | Crear, editar, ordenar, activar o desactivar y eliminar productos. |
| **Categorías** | Crear y eliminar las categorías del menú. |
| **Mesas** | Definir las mesas y generar sus códigos QR. |
| **WhatsApp y mensaje** | Número que recibe los pedidos, costo del domicilio y texto del mensaje. |
| **Negocio y promoción** | Nombre, dirección, horario, contacto, "Sobre nosotros" y promociones. |

Al terminar, pulsa **"Salir"**, sobre todo si usas un computador compartido.

### B1. Pedidos

La pantalla **Pedidos** muestra:

- **Tres tarjetas de resumen:**
  - **Pedidos hoy**: cantidad de pedidos de hoy, sin contar los cancelados.
  - **Vendido hoy**: suma de los totales de hoy, sin contar los cancelados.
  - **Pendientes**: pedidos que aún están en estado *pendiente*.
- **Filtro por estado**: *Todos*, *Pendiente*, *Confirmado*, *Entregado* o *Cancelado*. Se aplica al elegir la opción.
- **Tabla** con número (#), fecha y hora, cliente y teléfono, forma de entrega (con la mesa si aplica), total, estado y el botón **"Ver"**.
- La tabla muestra los **300 pedidos más recientes**. Si no hay pedidos, dice *"Todavía no hay pedidos."*

**Ver un pedido.** Al pulsar **"Ver"** se abre la página **Pedido #N** con los datos del cliente, la forma de entrega, la dirección (si es domicilio), la mesa (si aplica), el método de pago, la nota, la lista de productos con cantidades y totales, y el estado actual.

**Cambiar el estado.** Elige el nuevo estado en **"Estado del pedido"** y pulsa **"Actualizar estado"**. El sitio confirma: *"Estado actualizado."* Si el pedido ya no existe, muestra un error en lugar de confirmar. Los estados son:

| Estado | Cuándo usarlo |
|---|---|
| **Pendiente** | Recién llegado por WhatsApp. Estado inicial de todo pedido. |
| **Confirmado** | Ya hablaste con el cliente y aceptaste el pedido. |
| **Entregado** | Se entregó en la mesa, en tienda o a domicilio. |
| **Cancelado** | El pedido no se va a preparar. No suma en las cifras del día. |

Los estados no avisan al cliente; los cambios se hacen a mano.

**Descargar el CSV.** El botón **"Descargar CSV"** descarga todos los pedidos en un archivo que se abre en Excel:

- Usa punto y coma (;) como separador, y acentos correctos.
- Incluye número, fecha, cliente, teléfono, entrega, mesa, dirección, barrio, pago, nota, productos (por ejemplo *2x Cono Arequipe*), subtotal, domicilio, total y estado.
- Las celdas que empiezan con = + - o @ se protegen para que Excel no las ejecute como fórmulas.

### B2. Productos

En **Productos** aparece la lista completa del menú, con foto, nombre, distintivo, categoría, precio, estado y acciones. **El orden de esta lista es el orden en que el cliente ve los productos.**

**Crear un producto.** Pulsa **"+ Nuevo producto"** y completa el formulario:

| Campo | Regla |
|---|---|
| **Nombre** * | Obligatorio. Máximo 100 caracteres. |
| **Categoría** | Elige una existente, o "Sin categoría" (aparecerá bajo OTROS). |
| **Descripción** | Opcional. Máximo 300 caracteres. |
| **Precio (pesos)** * | Obligatorio y mayor a cero. Puedes escribir `9500` o `9.500`; los puntos se ignoran. |
| **Etiqueta** | Ninguna, **Favorito** o **Nuevo**. Aparece sobre la foto en el menú. |
| **Imagen** | JPG, PNG o WEBP, máximo 2 MB. El sistema revisa que sea una imagen real, no solo su extensión. |
| **Disponible** | Casilla marcada = se vende. Desmarcada = no aparece para el cliente. |

Al guardar, el sitio responde: *"Producto guardado. El menú del cliente ya se actualizó."* No hace falta reimprimir los QR por cambios de precio o de producto.

**Acciones sobre la lista:**

- **↑ / ↓**: suben o bajan el producto en el menú. La flecha ↑ está desactivada en el primero y la ↓ en el último.
- **Editar**: cambia cualquier campo del formulario.
- **Pastilla de estado** (*Disponible* / *No disponible*): un clic la cambia sin abrir el formulario. Un producto no disponible deja de mostrarse y, si un cliente lo tenía en el carrito, se quita y el cliente recibe el aviso.
- **Eliminar** (ícono de papelera): pide confirmación. **No se puede recuperar.** Si el producto aparecía en pedidos anteriores, esos pedidos siguen guardados con sus datos. Si solo quieres ocultarlo, usa *No disponible*.

### B3. Categorías

En **Categorías** puedes:

- **Crear** una categoría escribiendo su nombre (máximo 60 caracteres). Aparece: *"Categoría creada."*
- **Eliminar** una categoría con el botón correspondiente, después de confirmar. Aparece: *"Categoría eliminada."*

**Regla importante:** una categoría con productos **no se puede borrar**. El sistema responde: *"No se puede borrar: tiene productos. Cámbialos de categoría primero."* Primero cambia esos productos a otra categoría en **Productos** y luego elimina la vacía.

Los productos sin categoría aparecen en el menú bajo el título **OTROS**.

### B4. Mesas y códigos QR

En **Mesas** se definen las mesas del local. Cada una tiene su código QR, que al escanearlo abre el menú con esa mesa ya elegida.

**Opción 1 · Escribir la lista.** En **"Mesas (una por línea)"** escribe una mesa por renglón, por ejemplo:

```
Mesa 1
Mesa 2
Barra
Terraza 2
```

Pulsa **"Guardar mesas"**. El sistema responde *"Mesas guardadas: N. Imprime los QR nuevos."*

> **Atención:** **Guardar mesas reemplaza la lista completa.** Una mesa que quites del texto se elimina y su QR deja de funcionar. Los nombres repetidos se cuentan una sola vez, las líneas vacías se ignoran y cada nombre se recorta a 40 caracteres. Debe quedar al menos una mesa.

**Opción 2 · Generar rápido.** En **"Generar rápido"** escribe una cantidad entre 1 y 100 y pulsa **"Crear «Mesa 1» a «Mesa N»"**. Crea las mesas numeradas que falten; **las que ya existen no se duplican ni se cambian**. Aparece *"Listo: Mesa 1 a Mesa N."*

**Los códigos QR.** A la derecha se muestra un QR por mesa, con su nombre y el enlace **"Abrir enlace"**.

- Imprime cada QR y pégalo en la mesa, en un lugar visible.
- Los QR apuntan a la dirección que tengas abierta en ese momento (por ejemplo `http://127.0.0.1:8000/index.php?mesa=Mesa+5`). La pantalla te muestra la dirección actual.
- Si cambias el dominio al publicar el sitio, **genera los QR de nuevo** e imprímelos.
- Los QR se dibujan en el navegador, así que se pueden ver sin internet.

### B5. WhatsApp y mensaje del pedido

En **WhatsApp y mensaje** se configura lo que llega al negocio.

1. **Número de WhatsApp.** Escribe el código de país seguido del celular, sin espacios ni signos. Para Colombia, el código es **57**: por ejemplo `573245245615`. Debe tener **al menos 10 dígitos**; si no, aparece *"El número de WhatsApp debe incluir el código de país."*
2. **Costo del domicilio ($).** Escribe el valor en pesos, sin decimales. Escribe **0** si el domicilio es gratis. Si lo dejas vacío se guarda como 0. El valor inicial del sistema es $5.000.
3. **Mensaje que se envía.** Es el texto que recibe el negocio. Las palabras entre llaves `{ }` se reemplazan solas con los datos del pedido:

   | Marca | Qué pone |
   |---|---|
   | `{negocio}` | Nombre del negocio. |
   | `{pedido}` | Una línea por producto con cantidad y total de la línea. |
   | `{subtotal}` | Suma de los productos. |
   | `{domicilio}` | Costo del domicilio, o *No aplica*. |
   | `{total}` | Subtotal más domicilio. |
   | `{nombre}` | Nombre del cliente. |
   | `{telefono}` | Teléfono del cliente. |
   | `{entrega}` | Domicilio, Recoger en tienda o En mi mesa (con el número de mesa). |
   | `{direccion}` | Dirección y barrio (si es domicilio), o *No aplica*. |
   | `{pago}` | Efectivo, Nequi o Transferencia. |
   | `{nota}` | La nota del cliente, o *Ninguna*. |

   El mensaje no puede quedar vacío.

4. **Vista previa.** A la derecha se ve cómo llegará el mensaje, usando un pedido de ejemplo. Revísala antes de guardar.
5. Pulsa **"Guardar"**. Aparece *"Configuración de WhatsApp guardada."*

Los cambios de número o de mensaje aplican a los pedidos nuevos, no a los que ya se enviaron.

### B6. Negocio y promoción

En **Negocio y promoción** se guardan los datos que aparecen en **Información** y en el pie de página:

| Campo | Regla |
|---|---|
| **Nombre del negocio** * | Obligatorio. Máximo 80 caracteres. Aparece en el logo y en el pie. |
| **Sobre nosotros** | Texto libre, máximo 1.500 caracteres. Aparece en Información. |
| **Dirección** | Máximo 160 caracteres. |
| **Horario** | Máximo 120 caracteres. Por ejemplo: *Lunes a domingo · 11:00 a. m. – 10:00 p. m.* |
| **Teléfono** | Máximo 40 caracteres. |
| **Correo** | Debe ser un correo válido, o quedar vacío. Si está vacío, no aparece en el pie. |

Los campos vacíos muestran *"Por completar"* en lugar de dejar espacios en blanco. Pulsa **"Guardar datos"**: aparece *"Datos del negocio guardados."*

**Promociones.** Esta parte del panel está pendiente de confirmar su funcionamiento. Por ahora no se aplica ninguna promoción automática al carrito ni al pedido.

### B7. Ver el menú y cerrar sesión

- **"Ver menú ↗"** abre el menú del cliente en otra pestaña. Úsalo para revisar exactamente lo que ve el cliente después de un cambio.
- **"Salir"** cierra tu sesión y regresa a la pantalla de entrada. El sistema confirma: *"Sesión cerrada."*

### B8. Contraseña y mantenimiento básico

- **Cambiar la contraseña del panel:** en el equipo donde está el sitio, ejecuta `php bin/setup.php --clave=nueva-clave`. Solo cambia la contraseña y **no se borran datos**. Mínimo 6 caracteres.
- **Empezar de cero:** `php bin/setup.php --reiniciar`. **Borra toda la información**: productos, categorías, mesas, pedidos guardados y los datos del negocio. Úsalo solo para una instalación nueva; antes descarga el CSV de pedidos si lo necesitas.
- Las fotos subidas quedan en la carpeta `public/uploads/`; el reinicio no las borra.

---

## Parte C · Solución de problemas (administrador)

| Situación | Causa probable | Qué hacer |
|---|---|---|
| **Veo "Contraseña incorrecta."** | Escribiste mal la clave. | Revísala. Si no la recuerdas, cámbiala con `php bin/setup.php --clave=nueva-clave` desde el equipo del sitio. |
| **Un QR no abre el menú en el celular** | La dirección del QR es `127.0.0.1` o `localhost`, que el celular no alcanza. | Publica el sitio o ábrelo con la IP del equipo en la misma red Wi-Fi, y genera los QR de nuevo. |
| **Desaparecieron mesas** | Se usó **Guardar mesas** con una lista que no las incluía. | Vuelve a escribirlas y guarda, o usa **Generar rápido**. Imprime los QR de las mesas nuevas. |
| **No puedo borrar una categoría** | Tiene productos. | Cambia esos productos a otra categoría y vuelve a intentarlo. |
| **La foto no se sube** | Formato o tamaño no permitido. | Usa JPG, PNG o WEBP de máximo 2 MB. Reduce la foto si pesa más. |
| **El cambio no aparece en el menú del cliente** | El navegador tiene una versión anterior. | Recarga la página con el cliente (o abre **Ver menú ↗**). Revisa que el producto esté marcado como *Disponible*. |
| **Un producto no aparece para el cliente** | Está como *No disponible*, o está sin precio válido. | Revísalo en **Productos** y activa la casilla o la pastilla. |
| **Los pedidos del día no suman lo que esperaba** | Los cancelados no cuentan en *Vendido hoy*. | Revisa en **Pedidos** el filtro *Cancelado* y los estados de cada pedido. |
| **Un pedido no llegó por WhatsApp** | El cliente no envió el mensaje desde el enlace, o WhatsApp no se abrió. | El pedido igual queda guardado en **Pedidos** como *Pendiente*. Confírmalo con el cliente por teléfono o por WhatsApp y cambia su estado. |
| **Un cliente no puede elegir "En mi mesa"** | No escaneó el QR ni eligió mesa en el menú. | Pídele que escanee el QR de su mesa o que la elija en el menú con **Guardar**. |

---

## Glosario

| Término | Qué significa |
|---|---|
| **Carrito** | Lista de productos que el cliente va a pedir, con su total. Se guarda en el navegador. |
| **Categoría** | Grupo del menú, como Conos o Canastas. |
| **Domicilio** | Entrega en la dirección del cliente, con un costo que define el negocio. |
| **Estado del pedido** | Pendiente, Confirmado, Entregado o Cancelado. |
| **Mesa** | Lugar del local asociado a un código QR, para saber de dónde llegó el pedido. |
| **QR** | Código cuadrado que el celular lee con la cámara para abrir el menú con la mesa elegida. |
| **Llaves `{ }`** | Espacios del mensaje de WhatsApp que se llenan solos con los datos del pedido. |
| **Panel** | Parte privada del sitio para el administrador, protegida con contraseña. |
| **Disponible** | Producto que se puede vender y aparece en el menú. |

---

## Preguntas frecuentes

**¿Un cliente necesita cuenta para pedir?**
No. Solo llena el formulario de datos del pedido.

**¿Dónde se guardan los pedidos?**
En el sistema del negocio, en **Pedidos** del panel. Además llegan por WhatsApp.

**¿Puedo cambiar el número de WhatsApp?**
Sí, en **WhatsApp y mensaje**. Debe incluir el código de país, por ejemplo 57 para Colombia.

**¿Cómo cambio el precio de un producto?**
En **Productos**, pulsa **Editar**, cambia el precio y guarda. El menú se actualiza al instante.

**¿Qué pasa si un producto se agota?**
Márcalo como *No disponible*. Deja de aparecer para el cliente. Si un cliente lo tenía en el carrito, se quita automáticamente y el sitio le avisa.

**¿Hay que reimprimir los QR si cambio precios o productos?**
No. Los QR son de las mesas, no de los productos. Solo hay que volver a generarlos si cambia la dirección del sitio.

**¿Puedo pedir desde una mesa sin escanear el QR?**
Sí, eligiéndola en la caja del menú y pulsando **Guardar**. El QR es la forma más rápida.

**¿Qué pasa si el cliente cierra WhatsApp sin enviar?**
El pedido ya quedó guardado como *pendiente*, y su carrito sigue ahí para intentarlo otra vez.

**¿Puedo editar un pedido ya enviado?**
El sistema no edita pedidos. Si hay un cambio, comunícalo al cliente por WhatsApp y actualiza el estado. Si hay que anularlo, márcalo como *Cancelado*.

---

## Apéndice · Control de cambios (Git) para quien mantiene el sistema

Esta parte es para la persona que hace cambios en el código o en los textos. El detalle completo está en `LEEME.md`, sección 8.

**Ramas**

- `production`: lo que está en producción. Solo recibe cambios probados.
- `staging`: pruebas. Lo que se va a publicar se prueba aquí primero.
- `feature/…` y `fix/…`: trabajo de cada cambio, creado desde `staging`.

**Flujo de un cambio**

1. `git switch staging` y luego `git switch -c feature/nombre-corto`.
2. Hacer el cambio.
3. Ejecutar `bin/verificar.sh` (debe terminar con `TODO OK`) y probar en el navegador el flujo menú → carrito → datos → WhatsApp, y el panel.
4. Actualizar el **manual**, el **LEEME** y el **CHANGELOG** si el cambio afecta lo que ve el cliente o el administrador.
5. `git status` y `git diff` para revisar; `git add` solo de los archivos del cambio.
6. `git commit -m "tipo(área): descripción"`.
7. Publicar la rama y abrir un Pull Request a `staging` **solo cuando la persona responsable lo pida**.
8. Probar en `staging` y, después, pasar de `staging` a `production`.

**Mensajes de commit.** Formato `tipo(área): descripción`, en español, en imperativo y corto. Tipos: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`. Ejemplo: `fix(carrito): no permitir cantidades mayores a 99`.

**Cómo ver los cambios**

- `git log --oneline`: historial resumido.
- `git log --stat`: historial con los archivos que cambiaron.
- `git show <hash>`: qué cambió en un commit concreto.
- `git diff production staging --stat`: qué tiene `staging` que aún no está en `production`.
- `git log --oneline production..staging`: commits que faltan para publicar.

**Deshacer.** Para revertir un commit ya compartido usa `git revert <hash>`, que crea un commit nuevo que lo deshace. No uses `git reset --hard` ni `git push --force` sobre `production` ni `staging`.

**Qué nunca se versiona.** Bases de datos (`data/*.sqlite`), fotos subidas (`public/uploads/`), archivos `.env` y contraseñas. Tampoco se deben subir datos reales de clientes ni teléfonos a un commit.

**Historial actual.** Un commit inicial: `7fda0f0`, *feat(sitio): sitio de pedidos de Heladería Lupa*. Los cambios visibles se resumen en `CHANGELOG.md`.
