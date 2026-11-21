# Guía de estudio · Heladería Lupa

Esta guía explica el proyecto de punta a punta, con el código incluido. Está pensada para que la leas de corrido, en orden, y luego la uses para responder en la sustentación.

**Cómo leerla**

| Parte | Qué encuentras | Para qué |
|---|---|---|
| **Parte 1** | Qué es el proyecto y cómo funciona, sin código | Entender la idea general. |
| **Parte 2** | El código, archivo por archivo, en orden de lectura | Saber dónde está cada cosa y qué hace. |
| **Parte 3** | Seguridad, decisiones de diseño y limitaciones | Justificar el proyecto. |
| **Parte 4** | Preguntas probables, demostración, glosario y checklist | Prepararte para la sustentación. |

Si necesitas el detalle de cada función (línea exacta, quién la llama, etc.), está en `docs/referencia-del-codigo.md`. Esta guía te da lo que necesitas para explicarlo.

---

# Parte 1 · El proyecto

## 1.1 Qué es

Heladería Lupa es un **sitio web de pedidos** para una heladería en Medellín.

- **Para el cliente:** ve el menú, busca productos, los agrega al carrito, escribe sus datos y envía el pedido por **WhatsApp**. Si está en el local, puede escanear el **código QR** de su mesa.
- **Para el administrador:** entra con una contraseña a un **panel**, donde gestiona pedidos, productos, categorías, mesas, el número de WhatsApp y los datos del negocio.

**Problema que resuelve:** antes el pedido se tomaba por mensaje manual y no quedaba registro. Ahora cada pedido se guarda (con su estado y su total), el precio se calcula en el servidor y el mensaje de WhatsApp siempre tiene el mismo formato.

**Estado actual:** funciona en local. No está publicado en internet.

## 1.2 Tecnologías

| Parte | Tecnología | Por qué se eligió |
|---|---|---|
| Servidor | **PHP 8.1 o superior** | Fácil de instalar y se usa en casi todos los hostings. |
| Base de datos | **SQLite** (con PDO) | Es un solo archivo. No hay que instalar un servidor de base de datos. |
| Páginas | HTML y CSS | Lo que ve el navegador. |
| Interactividad | JavaScript sencillo | Solo mejoras: el sitio funciona sin él. |
| Dependencias | **Ninguna** | No hay Composer ni npm. Menos cosas que pueden fallar. |
| Control de cambios | **Git** | Permite volver atrás y trabajar por ramas. |

## 1.3 Cómo funciona, en palabras sencillas

Imagina una pizzería con un solo mostrador:

1. **El cliente pide algo** (pulsa un botón en la página). Eso envía un **formulario** al servidor.
2. **Un recepcionista** (`index.php`) recibe todas las peticiones y se fija en qué pidió el cliente.
3. **Un encargado** (un **controlador**) decide qué hacer: revisar, guardar, responder.
4. **El archivero** (las **funciones de `src/`**) es el que habla con la base de datos.
5. **La vista** (la **plantilla**) arma la página que el cliente ve.

Esa es la idea de todo el proyecto: **petición → recepcionista → encargado → archivero → página**.

---

# Parte 2 · El código, archivo por archivo

Lee los archivos en este orden. Cada uno usa el anterior.

## 2.0 Palabras clave antes de empezar

| Palabra | Qué significa aquí |
|---|---|
| **Función** | Un bloque de código con nombre que hace una tarea. Ej.: `e()` escapa texto. Se "llama" escribiendo su nombre con paréntesis. |
| **Parámetro** | Dato que la función recibe para trabajar. Ej.: `dinero(8000)` recibe `8000`. |
| **Retorno** | Lo que la función devuelve al terminar. Ej.: `dinero(8000)` devuelve `"$8.000"`. |
| **Variable** | Una caja con nombre que guarda un valor. Empieza con `$` en PHP. |
| **Constante** | Como una variable, pero su valor no cambia. Se escribe sin `$`. |
| **Arreglo (array)** | Una lista o un diccionario de valores. Ej.: `['Mesa 1', 'Mesa 2']`. |
| **Sesión** | Memoria del servidor para **un navegador**: guarda su carrito, si es admin, etc. |
| **GET** | Forma de pedir una página (la dirección que se ve en el navegador). |
| **POST** | Forma de enviar datos (formularios). Los datos no se ven en la dirección. |
| **Controlador** | Función que recibe una petición y decide qué hacer (archivos `ctrl_*.php`). |
| **Vista** | Plantilla HTML con un poco de PHP para mostrar datos (carpeta `views/`). |
| **Ruta** | Dirección interna que dice qué controlador atiende cada petición (`rutas.php`). |

## 2.1 `public/index.php` · la puerta de entrada

**Qué es:** el **único archivo** que el navegador visita directamente. Todas las páginas pasan por aquí.

**Qué hace, paso a paso:**

1. Carga `src/bootstrap.php` (prepara todo lo demás).
2. Si la dirección trae `?mesa=Mesa+5` (lo que pasa al escanear un QR), guarda la mesa en la sesión con `mesa_fijar()`. Si además no trae `?r=`, lleva al menú.
3. Lee `?r=` para saber qué página quiere (por ejemplo `menu`). Si no trae nada, usa `inicio`.
4. Llama a `despachar()`, que busca el controlador correcto.
5. Si algo sale muy mal, muestra "Algo salió mal" y escribe el error en la terminal.

**Idea clave:** un solo archivo de entrada hace que los QR funcionen sin configurar nada en el servidor.

## 2.2 `src/bootstrap.php` · el arranque

**Qué es:** prepara todo antes de que empiece cualquier página.

| Función o paso | Qué hace |
|---|---|
| `config()` (línea 9) | Lee `config/config.php` una sola vez y lo guarda. Si existe la variable `LUPA_DB_PATH`, usa esa ruta para la base (lo usan las pruebas). Devuelve un arreglo con todos los valores de configuración. |
| Zona horaria | Pone `America/Bogota` para que las fechas de los pedidos sean correctas. |
| Sesión | Si no es la terminal, inicia la sesión del navegador. La cookie es `httponly` (JavaScript no la puede leer) y `SameSite=Lax` (más protegida). |
| Carga de archivos | Trae, en orden: `util.php`, `db.php`, `tienda.php`, `carrito.php`, `pedidos.php`, `ctrl_publico.php`, `ctrl_admin.php` y `rutas.php`. |

## 2.3 `src/util.php` · herramientas de uso común

**Qué es:** funciones pequeñas que usa todo el sitio.

| Función | Línea | Qué hace, en simple | Dónde se usa |
|---|---|---|---|
| `e($valor)` | 5 | **Protege el texto** antes de mostrarlo en la página. Convierte `<` y `>` en texto normal, para que nadie pueda meter código. | En casi todas las vistas (146 veces). |
| `dinero($pesos)` | 11 | Convierte un número en precio: `8000` → `$8.000`. | Vistas, pedidos, carrito. |
| `url($ruta, $extra)` | 17 | Arma una dirección del sitio: `url('menu')` → `index.php?r=menu`. | Casi todas las páginas (82 veces). |
| `url_absoluta($params)` | 25 | Arma la dirección **completa** (con `http://` y el nombre del equipo). Se usa en los QR. | Panel de mesas. |
| `redirigir($destino)` | 34 | **Manda al navegador a otra página** y termina el programa. | Todos los controladores. |
| `flash($mensaje, $tipo)` | 41 | Guarda un **mensaje de aviso** para mostrarlo en la siguiente página. Si no le pasas texto, lo lee y lo borra. | Controladores y layouts. |
| `csrf_token()` | 52 | Crea (una vez) una **clave secreta** en la sesión. | Lo usa `csrf_campo()`. |
| `csrf_campo()` | 60 | Pone esa clave secreta dentro de cada formulario, como campo oculto. | Todos los formularios del sitio (21 veces). |
| `csrf_verificar()` | 66 | **Revisa la clave secreta** de un formulario. Si no coincide, detiene todo con error 419. | Todas las acciones que cambian datos (17 veces). |
| `render($vista, $datos, $layout)` | 77 | **Arma la página**: convierte los datos en variables, dibuja la vista y la mete dentro del marco (cabecera y pie). | Todos los controladores de página. |
| `es_admin()` | 88 | Dice si el navegador está logueado en el panel. | `requerir_admin()` y el login. |
| `requerir_admin()` | 94 | Si no está logueado, lo **manda al login**. | Al inicio de cada acción del panel (21 veces). |

**Lo que hay que saber de `render()`:** primero dibuja la vista (el contenido) en memoria, y después dibuja el marco con ese contenido adentro. Por eso la cabecera y el pie aparecen en todas las páginas sin repetir código.

## 2.4 `src/db.php` · la base de datos

**Qué es:** todo lo que habla con SQLite y los **ajustes** del negocio.

| Función | Línea | Qué hace, en simple |
|---|---|---|
| `db()` | 5 | **Abre la conexión** a la base la primera vez y la reutiliza después. Si algo falla, lanza un error claro. |
| `instalar_esquema($pdo)` | 26 | **Crea las tablas** si no existen: `categorias`, `productos`, `mesas`, `pedidos` y `ajustes`. |
| `ajuste($clave)` | 77 | **Lee un ajuste** (por ejemplo, el número de WhatsApp). Si no está guardado, usa el valor de `config.php`. |
| `ajustes_guardar($valores)` | 89 | **Guarda varios ajustes** a la vez. Si ya existen, los actualiza. |

**Dato clave:** `db()` activa las **claves foráneas** (`PRAGMA foreign_keys`), que son reglas que relacionan una tabla con otra.

### Las tablas

| Tabla | Para qué sirve | Columnas principales |
|---|---|---|
| `categorias` | Grupos del menú | `nombre` (único), `orden` |
| `productos` | Lo que se vende | `categoria_id`, `nombre`, `descripcion`, `precio` (entero), `imagen`, `etiqueta`, `disponible` (0 o 1), `orden` |
| `mesas` | Mesas del local | `nombre` (único), `orden` |
| `pedidos` | Pedidos enviados | cliente, `entrega`, `mesa`, `direccion`, `barrio`, `pago`, `nota`, `items` (JSON), `subtotal`, `domicilio`, `total`, `estado` |
| `ajustes` | Datos del negocio | `clave`, `valor` |

**Tres decisiones que debes poder explicar:**

- **El precio es un número entero.** Los pesos no usan centavos en la práctica, y así se evitan errores de redondeo.
- **Las líneas del pedido se guardan como texto JSON** (un formato de texto estructurado) dentro del pedido. Así el pedido es una "foto" de lo que se pidió: si después cambia el precio, el pedido no cambia.
- **La contraseña del panel nunca está en texto.** Se guarda cifrada en `ajustes` con la clave `admin_hash`.

## 2.5 `src/tienda.php` · categorías, productos, fotos y mesas

**Qué es:** la parte que administra el menú. Se usa en el panel y en el menú del cliente.

### Categorías

| Función | Línea | Qué hace, en simple |
|---|---|---|
| `categorias_listar()` | 6 | Devuelve las categorías y cuántos productos tiene cada una. |
| `categoria_crear($nombre)` | 14 | Crea una categoría. Si el nombre ya existe, avisa. |
| `categoria_eliminar($id)` | 28 | Borra una categoría, **pero solo si está vacía**. |

### Productos

| Función | Línea | Qué hace, en simple |
|---|---|---|
| `productos_listar($soloDisponibles)` | 40 | Devuelve los productos en el orden del menú. Con `true`, solo los disponibles. |
| `producto_obtener($id)` | 51 | Devuelve un producto por su número, o `null` si no existe. |
| `producto_guardar($datos, $id, $imagen)` | 63 | **Crea o actualiza** un producto. Revisa que tenga nombre, precio mayor a cero y una etiqueta válida. Si cambias la foto, borra la anterior. |
| `producto_alternar_disponible($id)` | 109 | Cambia el producto entre disponible y no disponible. |
| `producto_eliminar($id)` | 114 | Borra el producto **y su foto**. |
| `producto_mover($id, $direccion)` | 125 | Sube o baja un producto en el menú, intercambiando su lugar con el vecino. |
| `productos_filtrar($productos, $texto, $categoria)` | 236 | Filtra la lista por texto (nombre o descripción) y por categoría. No distingue mayúsculas. |

**Cómo funciona el orden:** cada producto tiene un número `orden`. `producto_mover()` intercambia dos números y después vuelve a numerar todos de 10 en 10, dentro de una **transacción** (si algo falla, no queda a medias).

### Fotos

| Función | Línea | Qué hace, en simple |
|---|---|---|
| `imagen_guardar($archivo)` | 148 | **Revisa la foto subida**: que pese 2 MB o menos, y que de verdad sea JPG, PNG o WEBP (revisa el contenido, no solo el nombre). Luego la guarda con un nombre al azar. |
| `imagen_borrar($ruta)` | 178 | Borra una foto de la carpeta de subidas. Solo borra archivos dentro de `uploads/`. |

### Mesas

| Función | Línea | Qué hace, en simple |
|---|---|---|
| `mesas_listar()` | 191 | Devuelve las mesas en orden. |
| `mesas_reemplazar($texto)` | 197 | Toma el texto con una mesa por línea, quita repetidas y vacías, y **reemplaza toda la lista**. Ojo: una mesa quitada pierde su QR. |
| `mesas_generar($n)` | 220 | Crea "Mesa 1" hasta "Mesa N" (de 1 a 100). No duplica las que ya existen. |

## 2.6 `src/carrito.php` · el carrito y la mesa

**Qué es:** el carrito del cliente. Vive en la **sesión** del navegador, no en la base de datos.

**La variable principal:** `$_SESSION['carrito']` es una lista donde la **clave es el número del producto** y el **valor es la cantidad**. Por ejemplo, `[3 => 2]` significa "2 unidades del producto 3".

| Función | Línea | Qué hace, en simple |
|---|---|---|
| `carrito_agregar($id)` | 10 | Suma una unidad. Solo si el producto existe y está disponible. Máximo 99. |
| `carrito_cambiar_cantidad($id, $cantidad)` | 22 | Cambia la cantidad. Si es 0 o menos, lo quita. |
| `carrito_quitar($id)` | 34 | Quita un producto del carrito. |
| `carrito_vaciar()` | 39 | Deja el carrito vacío. |
| `carrito_items()` | 48 | **Arma la lista con los datos reales** (nombre, precio, línea). Si un producto ya no está disponible, lo **quita** y avisa al cliente. |
| `carrito_totales($items)` | 93 | Suma los importes y las unidades. |
| `carrito_unidades()` | 105 | Cuenta las unidades para el número del icono del carrito. |
| `mesa_actual()` | 111 | Devuelve la mesa de la sesión, o `null` si no hay. |
| `mesa_fijar($nombre)` | 116 | Guarda la mesa en la sesión. |
| `mesa_limpiar()` | 122 | Quita la mesa de la sesión. |

**Dato importante para la sustentación:** los precios **nunca vienen del navegador**. `carrito_items()` los lee de la base en cada petición. Así, aunque alguien cambie el precio en su navegador, el servidor usa el precio real.

## 2.7 `src/pedidos.php` · pedidos y el mensaje de WhatsApp

**Qué es:** todo lo que pasa desde que el cliente envía el formulario hasta que el pedido queda guardado y WhatsApp se abre.

### Constantes (valores fijos)

| Constante | Línea | Valor | Para qué |
|---|---|---|---|
| `ENTREGAS` | 4 | domicilio, recoger, mesa | Las tres formas de recibir el pedido, con su nombre en español. |
| `PAGOS` | 10 | Efectivo, Nequi, Transferencia | Los métodos de pago válidos. |
| `ESTADOS_PEDIDO` | 114 | pendiente, confirmado, entregado, cancelado | Los estados válidos de un pedido. |

### Funciones

| Función | Línea | Qué hace, en simple |
|---|---|---|
| `pedido_validar($entrada)` | 16 | **Revisa el formulario**: nombre (mínimo 2 letras), teléfono (mínimo 7 dígitos), forma de entrega, pago, y si es domicilio, dirección y barrio. Devuelve los errores y los datos limpios. |
| `pedido_domicilio($entrega)` | 58 | Devuelve el costo del domicilio, solo si el pedido es a domicilio. |
| `pedido_lineas($items)` | 64 | Escribe una línea por producto: `• 2 x Cono Arequipe — $19.000`. |
| `pedido_mensaje($plantilla, $datos, $items)` | 77 | **Arma el mensaje de WhatsApp**: reemplaza las llaves `{total}`, `{nombre}`, etc. por los datos reales. |
| `whatsapp_url($numero, $mensaje)` | 106 | Arma el enlace `https://wa.me/...` con el mensaje ya escrito. Deja solo dígitos en el número y codifica el texto para que viaje bien en un enlace. |
| `pedido_totales($datos, $items)` | 122 | Calcula subtotal, domicilio y total. |
| `pedido_guardar($datos, $items)` | 130 | **Guarda el pedido** en la base con estado `pendiente`. Se hace **antes** de abrir WhatsApp. |
| `pedidos_listar($estado)` | 165 | Devuelve los últimos 300 pedidos, con filtro opcional por estado. |
| `pedido_obtener($id)` | 175 | Devuelve un pedido con sus productos ya leídos. |
| `pedido_cambiar_estado($id, $estado)` | 187 | Cambia el estado, solo si es uno de los válidos. |
| `pedidos_resumen_hoy()` | 196 | Cuenta los pedidos de hoy y suma sus totales, sin contar los cancelados. |
| `csv_celda($valor)` | 214 | Prepara una celda para Excel: pone comillas y protege contra fórmulas peligrosas. |
| `pedidos_csv()` | 224 | Arma el archivo CSV completo con todos los pedidos. |

**Orden importante:** `ctrl_pedido_enviar()` hace primero `pedido_validar()`, luego `pedido_guardar()` y al final `pedido_mensaje()` y `whatsapp_url()`. **Guardar antes de abrir WhatsApp** evita perder pedidos si el cliente no envía el mensaje.

## 2.8 `src/ctrl_publico.php` · páginas del cliente

**Qué es:** los controladores que atienden lo que ve el cliente. Cada uno responde a una ruta de `rutas.php`.

| Función | Qué hace, en simple | Página que muestra |
|---|---|---|
| `ctrl_inicio()` | Toma los primeros 4 productos disponibles para "Lo más pedido". | Inicio |
| `ctrl_menu()` | Lee el buscador y la categoría, filtra, agrupa por categoría (los sin categoría van a "Otros"). | Menú |
| `ctrl_mesa_elegir()` | Guarda o quita la mesa elegida en el menú. | (vuelve al menú) |
| `ctrl_info()` | Muestra la página de información del negocio. | Información |
| `ctrl_carrito()` | Lee el carrito y calcula los totales. | Carrito |
| `ctrl_carrito_accion()` | Agrega, cambia, quita o vacía productos. Verifica la clave secreta del formulario. | (vuelve a la página anterior) |
| `ctrl_pedido()` | Muestra el formulario de datos. Si había errores, los muestra junto a cada campo. | Datos del pedido |
| `ctrl_pedido_enviar()` | **El corazón del pedido**: valida, guarda, arma el mensaje y abre WhatsApp. | (abre WhatsApp) |
| `ctrl_no_encontrado()` | Muestra la página de error 404. | Página no encontrada |

**Dato clave:** si un formulario tiene errores, el servidor guarda los errores y los datos en la sesión y **redirige** de vuelta al formulario. Así el cliente no pierde lo que escribió. Este patrón se llama **Post-Redirect-Get**: después de enviar un formulario, siempre se redirige, para que recargar la página no repita la acción.

## 2.9 `src/ctrl_admin.php` · el panel del administrador

**Qué es:** los controladores del panel. Casi todos empiezan con `requerir_admin()`, que comprueba que haya sesión de administrador.

**Dos tipos de acciones:**

- **Lectura** (`GET`): muestran una pantalla.
- **Escritura** (`POST`): cambian datos. Siempre verifican la clave secreta (`csrf_verificar()`).

### Entrar y salir

| Función | Qué hace |
|---|---|
| `ctrl_admin_entrar()` | Si ya está logueado, va a productos. Si no, muestra el login. |
| `ctrl_admin_login()` | Revisa la contraseña con `password_verify()` (compara con el cifrado). Si es correcta, cambia el identificador de la sesión y marca `admin`. |
| `ctrl_admin_salir()` | Quita la marca de administrador y avisa "Sesión cerrada.". |

### Productos

| Función | Qué hace |
|---|---|
| `ctrl_admin_productos()` | Muestra la lista de productos. |
| `ctrl_admin_producto_form()` | Muestra el formulario para crear o editar. Si el guardado falló, muestra lo que se había escrito. |
| `ctrl_admin_producto_guardar()` | Sube la foto, guarda el producto. **Si algo falla, borra la foto recién subida** para no dejar archivos sueltos. |
| `ctrl_admin_producto_mover()` | Sube o baja un producto. |
| `ctrl_admin_producto_disponible()` | Activa o desactiva un producto. |
| `ctrl_admin_producto_eliminar()` | Borra un producto con su foto. |

### Categorías, mesas, WhatsApp, negocio y pedidos

| Función | Qué hace |
|---|---|
| `ctrl_admin_categorias()` | Muestra las categorías con su conteo. |
| `ctrl_admin_categoria_guardar()` | Crea una categoría. |
| `ctrl_admin_categoria_eliminar()` | Borra una categoría vacía. |
| `ctrl_admin_mesas()` | Muestra las mesas con su QR. |
| `ctrl_admin_mesas_guardar()` | Reemplaza la lista de mesas. |
| `ctrl_admin_mesas_generar()` | Crea mesas numeradas. |
| `ctrl_admin_whatsapp()` | Muestra el número, el costo y el mensaje con su vista previa. |
| `ctrl_admin_whatsapp_guardar()` | Guarda el número (solo dígitos, mínimo 10), el costo (entero) y el mensaje (no vacío). |
| `vista_mensaje($plantilla)` | Arma un pedido de **ejemplo** para la vista previa. Usa la misma función que el envío real. |
| `ctrl_admin_negocio()` | Muestra los datos del negocio. |
| `ctrl_admin_negocio_guardar()` | Guarda los datos del negocio. Exige nombre y revisa que el correo sea válido. |
| `ctrl_admin_pedidos()` | Muestra la lista de pedidos, con filtro y cifras del día. |
| `ctrl_admin_pedido()` | Muestra el detalle de un pedido. |
| `ctrl_admin_pedido_estado()` | Cambia el estado de un pedido. |
| `ctrl_admin_pedidos_csv()` | Descarga el archivo de Excel con todos los pedidos. |

**Función auxiliar:** `admin_datos($seccion, $extra)` (línea 6) indica qué pestaña del panel está activa.

### Inventario completo del panel

Cada función del panel tiene una ruta en `rutas.php`. Esta tabla sirve para ubicar cualquier acción rápido:

| Función | Ruta | Controlador | Vista |
|---|---|---|---|
| Entrar | `GET admin`, `POST admin/login` | `ctrl_admin_entrar`, `ctrl_admin_login` | `admin/login.php` |
| Salir | `POST admin/salir` | `ctrl_admin_salir` | (redirige) |
| Pedidos | `GET admin/pedidos` | `ctrl_admin_pedidos` | `admin/pedidos.php` |
| Ver pedido | `GET admin/pedido` | `ctrl_admin_pedido` | `admin/pedido.php` |
| Cambiar estado | `POST admin/pedido/estado` | `ctrl_admin_pedido_estado` | (redirige) |
| CSV | `GET admin/pedidos/csv` | `ctrl_admin_pedidos_csv` | (descarga) |
| Productos | `GET admin/productos` | `ctrl_admin_productos` | `admin/productos.php` |
| Crear o editar | `GET admin/producto` | `ctrl_admin_producto_form` | `admin/producto_form.php` |
| Guardar producto | `POST admin/producto` | `ctrl_admin_producto_guardar` | (redirige) |
| Ordenar | `POST admin/producto/mover` | `ctrl_admin_producto_mover` | (redirige) |
| Disponibilidad | `POST admin/producto/disponible` | `ctrl_admin_producto_disponible` | (redirige) |
| Eliminar producto | `POST admin/producto/eliminar` | `ctrl_admin_producto_eliminar` | (redirige) |
| Categorías | `GET admin/categorias` | `ctrl_admin_categorias` | `admin/categorias.php` |
| Crear categoría | `POST admin/categorias` | `ctrl_admin_categoria_guardar` | (redirige) |
| Eliminar categoría | `POST admin/categorias/eliminar` | `ctrl_admin_categoria_eliminar` | (redirige) |
| Mesas | `GET admin/mesas` | `ctrl_admin_mesas` | `admin/mesas.php` |
| Guardar mesas | `POST admin/mesas` | `ctrl_admin_mesas_guardar` | (redirige) |
| Generar mesas | `POST admin/mesas/generar` | `ctrl_admin_mesas_generar` | (redirige) |
| WhatsApp | `GET admin/whatsapp` | `ctrl_admin_whatsapp` | `admin/whatsapp.php` |
| Guardar WhatsApp | `POST admin/whatsapp` | `ctrl_admin_whatsapp_guardar` | (redirige) |
| Negocio | `GET admin/negocio` | `ctrl_admin_negocio` | `admin/negocio.php` |
| Guardar negocio | `POST admin/negocio` | `ctrl_admin_negocio_guardar` | (redirige) |

## 2.10 `src/rutas.php` · la tabla de rutas

**Qué es:** una tabla donde cada **clave** es una combinación de método y página (por ejemplo `POST carrito`), y cada **valor** es el nombre del controlador que la atiende.

**La función:** `despachar($ruta, $metodo)` (línea 8) arma la clave, la busca en la tabla y ejecuta el controlador. Si no la encuentra, muestra la página 404.

**Cuántas rutas hay:** 31 en total: 8 públicas y 23 del panel.

**Por qué es útil:** para agregar una página nueva, solo hay que agregar una línea aquí. No hay que tocar `index.php`.

## 2.11 `views/` · las plantillas

**Qué son:** archivos HTML con un poco de PHP. **No hacen consultas a la base**: reciben los datos desde el controlador.

**Las carpetas:**

| Carpeta | Contenido |
|---|---|
| `layouts/` | El **marco** de las páginas: `publico.php` (cabecera, pie y botón de WhatsApp del cliente) y `admin.php` (barra del panel con las seis pestañas). |
| `publico/` | Las páginas del cliente: inicio, menú, carrito, pedido, información y 404. |
| `admin/` | Las páginas del panel: login, productos, formulario de producto, categorías, mesas, WhatsApp, negocio, pedidos y pedido. |
| `parciales/` | Piezas que se repiten: la tarjeta de producto, el logo y el dibujo del cono. |

**Variables más importantes:**

| Vista | Variables que recibe |
|---|---|
| Menú (`publico/menu.php`) | `$grupos` (productos por categoría), `$categorias`, `$texto` (lo buscado), `$categoria`, `$total`, `$mesas` |
| Carrito (`publico/carrito.php`) | `$items` (líneas del carrito), `$totales` |
| Pedido (`publico/pedido.php`) | `$items`, `$totales`, `$errores`, `$datos`, `$mesa`, `$costoDomicilio` |
| Productos del panel (`admin/productos.php`) | `$productos` |
| Formulario de producto (`admin/producto_form.php`) | `$producto`, `$categorias` |
| Pedidos del panel (`admin/pedidos.php`) | `$pedidos`, `$resumen`, `$estado` |
| Detalle de pedido (`admin/pedido.php`) | `$pedido` (con sus productos en `lineas`) |

**Dos reglas que debes saber:**

- Las vistas **escapan todo** con `e()` antes de mostrarlo. Por eso el marco puede imprimir el contenido sin escaparlo otra vez.
- Las variables del layout (`$titulo`, `$seccion`, `$aviso`) vienen de `render()`.

---

## 2.12 Variables del sistema, en una sola tabla

Estas son las "cajas" que guardan información mientras el sitio funciona.

### Sesión (la memoria de cada navegador)

| Variable | Qué guarda | Quién la escribe | Quién la lee |
|---|---|---|---|
| `$_SESSION['carrito']` | Productos y cantidades del cliente | `carrito_agregar`, `carrito_cambiar_cantidad`, `carrito_quitar`, `carrito_vaciar` | `carrito_items`, `carrito_unidades` |
| `$_SESSION['mesa']` | Mesa del cliente | `mesa_fijar` | `mesa_actual`, pedidos |
| `$_SESSION['csrf']` | Clave secreta de los formularios | `csrf_token` | `csrf_verificar` |
| `$_SESSION['flash']` | Aviso de una sola vez | `flash` | `flash` (lo borra al leerlo) |
| `$_SESSION['admin']` | Si está logueado en el panel | `ctrl_admin_login` | `es_admin`, `ctrl_admin_salir` |
| `$_SESSION['pedido_errores']` | Errores del formulario de pedido | `ctrl_pedido_enviar` | `ctrl_pedido` |
| `$_SESSION['pedido_datos']` | Datos escritos en el pedido | `ctrl_pedido_enviar` | `ctrl_pedido` |
| `$_SESSION['producto_viejo']` | Lo escrito en el formulario de producto si falló | `ctrl_admin_producto_guardar` | `ctrl_admin_producto_form` |

### Datos que llegan de los formularios

| Variable | De qué formulario viene | Qué significa |
|---|---|---|
| `$_POST['_csrf']` | Todos los formularios | La clave secreta. Debe coincidir. |
| `$_POST['accion']` | Carrito | `agregar`, `cambiar`, `quitar` o `vaciar`. |
| `$_POST['id']` | Carrito y panel | Número del producto o pedido. |
| `$_POST['mesa']` | Menú | Mesa elegida. Vacío = para llevar. |
| `$_GET['r']` | Cualquier enlace | Página que se quiere ver. |
| `$_GET['mesa']` | Código QR | Mesa escaneada. |
| `$_GET['q']` y `$_GET['cat']` | Buscador del menú | Texto y categoría para filtrar. |
| `$_FILES['imagen']` | Formulario de producto | Foto subida. |

### Configuración

| Variable | Valor | Para qué |
|---|---|---|
| `cantidad_maxima` | 99 | Máximo de unidades por producto en el carrito. |
| `max_upload_bytes` | 2 MB | Tamaño máximo de una foto. |
| `zona_horaria` | America/Bogota | Fecha y hora correctas. |
| `db_path` | data/lupa.sqlite | Ruta de la base de datos. |
| `uploads_dir` | public/uploads | Carpeta de las fotos. |
| `sesion_nombre` | lupa_sess | Nombre de la cookie de sesión. |
| `ajustes` | arreglo | Valores iniciales del negocio y del mensaje. |

---

## 2.13 Trazas: cómo se ejecutan las acciones principales

Una **traza** es seguir una acción paso a paso, desde que el usuario la pide hasta que ve el resultado.

### Traza 1 · Agregar un producto al carrito

1. El cliente pulsa el botón del carrito en una tarjeta. Se envía un formulario `POST` a `index.php?r=carrito`.
2. `index.php` busca la ruta `POST carrito` en `rutas.php`.
3. `ctrl_carrito_accion()` revisa la clave secreta (`csrf_verificar`) y decide qué hacer según `accion`.
4. `carrito_agregar()` busca el producto en la base (`producto_obtener`). Si está disponible, suma uno al carrito de la sesión.
5. El controlador redirige a la página de donde venía.
6. El menú se vuelve a armar y muestra el aviso "Agregado: …".

### Traza 2 · Enviar un pedido

1. El cliente llena el formulario y lo envía (`POST pedido`).
2. `ctrl_pedido_enviar()` revisa la clave secreta y lee el carrito (`carrito_items`). Si está vacío, lo manda al carrito.
3. `pedido_validar()` revisa los datos. Si hay errores, los guarda en la sesión y regresa al formulario.
4. Si todo está bien, `pedido_guardar()` **guarda el pedido** en la base con estado `pendiente`.
5. `pedido_mensaje()` arma el texto usando la plantilla del panel.
6. `whatsapp_url()` arma el enlace y el servidor redirige a WhatsApp.
7. El carrito **no se vacía**, por si WhatsApp no se abre.

### Traza 3 · Guardar un producto con foto

1. El administrador envía el formulario del producto (`POST admin/producto`).
2. `ctrl_admin_producto_guardar()` revisa que sea admin y la clave secreta.
3. `imagen_guardar()` revisa el tamaño y el tipo de la foto, y la guarda con un nombre al azar.
4. `producto_guardar()` valida los datos y crea o actualiza el producto. Si cambió la foto, borra la anterior.
5. Si algo falla, **borra la foto nueva**, guarda lo escrito y regresa al formulario.

### Traza 4 · Entrar al panel

1. El administrador abre `?r=admin` y ve el formulario de contraseña.
2. `ctrl_admin_login()` revisa la clave secreta y compara la contraseña con `password_verify()`.
3. Si es correcta, cambia el identificador de la sesión (por seguridad) y marca `$_SESSION['admin']`.
4. Desde ahí, cada acción del panel empieza con `requerir_admin()`, que verifica esa marca.

---

## 2.14 Pruebas automáticas

El archivo `tests/run.php` tiene **50 comprobaciones** agrupadas en 16 bloques. Se ejecutan con `bin/verificar.sh`, que además revisa la sintaxis de todos los archivos PHP.

Las pruebas usan una **base temporal**, así que nunca tocan los datos reales.

| Bloque | Qué comprueba |
|---|---|
| 1 · Dinero | `dinero()` pone el punto de miles. |
| 2 · Escape | `e()` protege el HTML. |
| 3 · Producto | Un precio con puntos se guarda como número entero. |
| 4 · Orden | Subir y bajar productos, incluso en los extremos. |
| 5 · Carrito | Sumar, el límite de 99 y quitar. |
| 6 · Totales | Subtotal y unidades. |
| 7 · Agotado | Un producto que se agota sale del carrito. |
| 8 · Pedido | Validación de nombre, teléfono, entrega, pago, domicilio y mesa. |
| 9 · Mensaje | El mensaje de WhatsApp queda completo. |
| 10 · Enlace | El número solo tiene dígitos y el mensaje va codificado. |
| 11 · Mesas | Sin repetidas ni vacías. |
| 12 · Categorías | No se borra una categoría con productos. |
| 13 · QR | El enlace del QR lleva el número de mesa. |
| 14 · Filtro | La búsqueda por texto y por categoría. |
| 15 · Pedidos | Estados, CSV y protección contra fórmulas. |
| 16 · Marcado | Cada `<section>` del menú se cierra. Es la prueba del error que se corrigió. |

**Regla del proyecto:** si corriges un error, primero escribes una prueba que falle con el código anterior, y después corriges.

---

## 2.15 JavaScript y CSS

### `public/assets/app.js`

Es un archivo corto. Todo está dentro de una función que se ejecuta sola, así que no ensucia el resto del sitio con variables.

| Parte | Qué hace |
|---|---|
| Botón "Volver arriba" | Aparece al bajar 400 píxeles y lleva al inicio con suavidad. |
| Confirmaciones | Pide confirmación antes de borrar o vaciar. |
| Pedido | Muestra la dirección solo si es domicilio y recalcula el total. |
| Códigos QR | Dibuja los QR del panel con la librería incluida. |

**Dato clave:** el sitio funciona sin JavaScript para comprar. El JavaScript solo mejora la experiencia.

### `public/assets/style.css`

- **Colores y medidas guardados en variables** al inicio del archivo (`--morado`, `--rosa`, `--fuente`, etc.). Cambiar un color en un solo lugar cambia todo el sitio.
- **Diseño para celular:** reglas para pantallas de 900 y 700 píxeles o menos.
- **Movimiento reducido:** respeta la preferencia del sistema operativo de quitar animaciones.

---

# Parte 3 · Seguridad, decisiones y limitaciones

## 3.1 Seguridad: qué protección hay y dónde

| Riesgo | Cómo se evita | Dónde está |
|---|---|---|
| **Código inyectado en la página** (XSS) | Todo se escapa con `e()` antes de mostrarlo. | `util.php`, todas las vistas |
| **Acciones falsas desde otra web** (CSRF) | Cada formulario lleva una clave secreta que el servidor verifica. | `csrf_campo()`, `csrf_verificar()` |
| **Inyección SQL** | Las consultas usan `?` y los datos se pasan aparte. | `tienda.php`, `pedidos.php`, `db.php` |
| **Precio cambiado desde el navegador** | El precio se lee de la base en cada petición. | `carrito_items()` |
| **Entrar al panel sin permiso** | Cada acción verifica la sesión. La contraseña se guarda cifrada. | `requerir_admin()`, `ctrl_admin_login()` |
| **Robar la sesión de otro** | Al entrar al panel se cambia el identificador de la sesión. | `ctrl_admin_login()` |
| **Subir archivos peligrosos** | Límite de tamaño y revisión del contenido real de la imagen. | `imagen_guardar()` |
| **Cookie leída por JavaScript** | La cookie de sesión es `httponly`. | `bootstrap.php` |
| **Fórmulas maliciosas en Excel** | Las celdas que empiezan con `=`, `+`, `-` o `@` se neutralizan. | `csv_celda()` |
| **Subir datos sensibles a GitHub** | `.gitignore` excluye la base, las fotos y el archivo `.env`. | `.gitignore` |

## 3.2 Decisiones de diseño y por qué

| Decisión | Por qué | Costo |
|---|---|---|
| Sin framework | Es más fácil de entender y de instalar. | Hay que escribir más código a mano. |
| Un solo punto de entrada | Los QR funcionan sin configurar el servidor. | Las direcciones son `?r=…`. |
| SQLite | Un archivo, sin servidor aparte. | No es para muchos usuarios a la vez. |
| Carrito en la sesión | Es temporal y de cada cliente. | Si se cierra el navegador, se pierde. |
| Pedido guardado antes de WhatsApp | Ningún pedido se pierde. | Puede haber pedidos que nunca se confirmaron. |
| Precio como número entero | Evita errores de redondeo. | Hay que mostrarlo siempre con `dinero()`. |
| Pedido con sus productos en JSON | El pedido no cambia si cambia el producto. | Es menos fácil hacer consultas por producto. |
| Post-Redirect-Get | Recargar no repite la acción. | Una redirección extra. |

## 3.3 Limitaciones honestas

Mencionarlas tú mismo demuestra que entiendes el sistema:

1. **Sin límite de intentos al entrar al panel.** Alguien podría probar contraseñas sin freno.
2. **Sin HTTPS.** Pensado para uso local. Para publicarlo hace falta un certificado.
3. **Un solo administrador.** No hay roles (por ejemplo, alguien que solo vea pedidos).
4. **Sin avisos automáticos.** El cliente no recibe nada cuando cambia el estado de su pedido.
5. **Promociones pendientes.** La sección existe, pero no aplica descuentos.
6. **Texto de pago inconsistente.** El sitio dice "al recibir o por transferencia", pero también ofrece Nequi.
7. **El cambio de estado no revisa que el pedido exista.** El panel dice "Estado actualizado." aunque no cambie nada.
8. **Guardar mesas reemplaza toda la lista.** Una mesa quitada pierde su QR.
9. **La sesión del panel no caduca por tiempo.** Solo se cierra con "Salir" o al cerrar el navegador.
10. **Solo 300 pedidos en el listado.** Con mucho volumen haría falta paginar.

---

# Parte 4 · Para la sustentación

## 4.1 Preguntas probables y respuestas cortas

**¿Por qué PHP y no un framework?**
Para un negocio pequeño, PHP sin framework se entiende de principio a fin y se instala en casi cualquier hosting.

**¿Por qué SQLite?**
Es un solo archivo, no necesita servidor y se respalda copiándolo.

**¿Cómo evitas que el cliente cambie el precio?**
El formulario solo envía el número del producto y la cantidad. El precio se lee de la base en cada petición.

**¿Qué pasa si se cae WhatsApp o el cliente cierra la ventana?**
El pedido ya está guardado como `pendiente`, y el carrito sigue ahí.

**¿Cómo funciona el QR?**
Lleva a `index.php?mesa=Mesa+5`. Al abrirlo, el sitio guarda la mesa en la sesión. Si el cliente pide "En mi mesa", la validación comprueba que esa mesa esté guardada.

**¿Qué es CSRF y cómo lo evitas?**
Es cuando otra web hace que tu navegador envíe una acción a nuestro sitio. Lo evito con una clave secreta en la sesión que cada formulario debe devolver.

**¿Qué es XSS y cómo lo evitas?**
Es meter código en la página a través de datos del usuario. Lo evito escapando todo con `e()`.

**¿Por qué el precio es un entero?**
Con decimales aparecen errores como `0.1 + 0.2 = 0.30000000000000004`. Con enteros la suma siempre es exacta.

**¿Cómo se valida una foto?**
Tamaño de 2 MB o menos, y se revisa el contenido real con `getimagesize()`, no solo el nombre.

**¿Cómo se prueba?**
Con `bin/verificar.sh`: revisa la sintaxis y corre las 50 pruebas en una base temporal.

**¿Qué hiciste cuando encontraste un error?**
Por ejemplo, la sección del menú que no se cerraba. Primero escribí una prueba que fallaba, luego corregí y comprobé que la prueba pasara.

**¿Por qué ramas `production`, `staging` y `feature/…`?**
Para que lo que está en producción siempre esté probado. Cada cambio se prueba en `staging` antes de pasar a `production`.

**¿Cómo deshaces un cambio ya subido?**
Con `git revert`, que crea un commit nuevo que lo deshace. No se usa `reset --hard` porque reescribe el historial compartido.

## 4.2 Demostración sugerida (unos 13 minutos)

1. **Cliente (2 min):** Inicio → Menú → buscar "arequipe" → agregar dos → Carrito → datos → elegir domicilio y ver cómo sube el total → enviar. Mostrar el mensaje de WhatsApp.
2. **Mesa (1 min):** elegir una mesa en el menú y mostrar cómo cambia el pedido.
3. **Panel (3 min):** entrar → Pedidos (el pedido nuevo y las cifras) → cambiar estado a *Confirmado* → Descargar CSV → Productos: cambiar un precio y desactivar uno → Mesas: mostrar el QR.
4. **Código (3 min):** abrir `public/index.php` y `src/rutas.php`; seguir la Traza 1 hasta `carrito_agregar()`; mostrar `csrf_verificar()` y una consulta con `?`.
5. **Pruebas y Git (2 min):** `bin/verificar.sh` (debe decir `TODO OK`) y `git log --oneline`.

**Consejo:** prepara un pedido de prueba antes y ten a mano la contraseña del panel.

## 4.3 Glosario

| Término | Significado en el proyecto |
|---|---|
| **Función** | Bloque de código con nombre que hace una tarea. |
| **Parámetro / retorno** | Lo que la función recibe / lo que devuelve. |
| **Controlador** | Función que decide qué hacer con una petición. |
| **Vista** | Plantilla que muestra los datos. |
| **Layout** | Marco común (cabecera y pie). |
| **Ruta** | Dirección interna que lleva a un controlador. |
| **Sesión** | Memoria del servidor para cada navegador. |
| **GET / POST** | Pedir una página / enviar datos. |
| **CSRF** | Ataque con acciones falsas; se evita con una clave secreta. |
| **XSS** | Inyección de código en la página; se evita escapando. |
| **PDO** | Forma segura de hablar con la base de datos. |
| **Transacción** | Varios cambios que se aplican juntos o no se aplican. |
| **Hash** | Versión cifrada e irreversible de la contraseña. |
| **JSON** | Formato de texto estructurado para guardar listas. |
| **Commit** | Un punto guardado en el historial de Git. |
| **Rama** | Línea de trabajo paralela en Git. |

## 4.4 Lista de verificación antes de sustentar

- [ ] `bin/verificar.sh` dice **TODO OK** (50 pruebas).
- [ ] Puedo iniciar el sitio y entrar al panel.
- [ ] Sé ubicar cada archivo y sus funciones principales (Parte 2).
- [ ] Puedo seguir las cuatro trazas (sección 2.13).
- [ ] Puedo nombrar al menos cuatro protecciones y dónde están (sección 3.1).
- [ ] Conozco las limitaciones y cómo mejorarlas (sección 3.3).
- [ ] Tengo probado el guion de la demostración (sección 4.2).
- [ ] Tengo a mano la contraseña del panel.

## 4.5 Mapa de documentos del proyecto

| Documento | Para qué |
|---|---|
| `docs/guia-de-estudio.md` (este) | Estudiar el proyecto y prepararte para sustentar. |
| `docs/referencia-del-codigo.md` | Detalle técnico de cada función, con línea exacta. |
| `LEEME.md` | Instalar, ejecutar, probar y usar Git. |
| `docs/manual-usuario.md` | Cómo usan el sitio el cliente y el administrador. |
| `CHANGELOG.md` | Qué cambió en cada versión. |
| `AGENTS.md` | Reglas de código y de trabajo. |
