# Referencia del código · Heladería Lupa

Esta referencia documenta el código **función por función**: dónde está cada una (archivo y línea), qué recibe, qué devuelve, qué hace y quién la llama. También documenta las **variables** (de sesión, de formularios, de configuración), las **constantes** y las **trazas** de ejecución de las acciones principales.

Las líneas corresponden a la versión actual del repositorio. Si el código cambia, las líneas pueden moverse; los nombres de las funciones no.

**Cómo usarla en la sustentación:** cuando te pregunten "¿dónde está…?", busca la función en la sección 4 y ve al archivo y línea indicados. Cuando te pregunten "¿qué pasa cuando…?", sigue la traza de la sección 6.

---

## 1. Convenciones del proyecto

- **Idioma:** los nombres de funciones, variables y textos están en español. Los nombres de funciones siguen un patrón: `ctrl_` (controlador), `pedido_` / `pedidos_` (pedidos), `producto_` / `productos_` (catálogo), `mesa_` / `mesas_` (mesas), `carrito_` (carrito), `categoria_` (categorías), `imagen_` (fotos), `csv_` (exportación).
- **Archivos con `declare(strict_types=1);`:** todos los archivos PHP de `src/`, `public/` y `bin/` lo declaran. Los tipos de los parámetros y del retorno son estrictos.
- **Sin clases ni objetos:** el código es procedimental. Lo único orientado a objetos es el uso de `PDO` y las excepciones.
- **Dinero en pesos enteros** (`int`). Solo se formatea para mostrar, con `dinero()`.
- **Escapar toda salida HTML** con `e()`.
- **Consultas siempre con parámetros** (`?`).

---

## 2. Variables

### 2.1 Variables de sesión (`$_SESSION`)

La sesión se inicia en `src/bootstrap.php`, solo fuera de la línea de comandos. Cada navegador tiene su propia sesión.

| Clave | Tipo | Quién la escribe | Quién la lee | Para qué |
|---|---|---|---|---|
| `carrito` | `array<int,int>` (id producto → cantidad) | `carrito_agregar`, `carrito_cambiar_cantidad`, `carrito_quitar`, `carrito_vaciar`, `carrito_items` (la limpia) | `carrito_items`, `carrito_unidades` | El carrito del cliente. |
| `mesa` | `string` | `mesa_fijar` (desde `ctrl_mesa_elegir` o desde `public/index.php` con `?mesa=`) | `mesa_actual`, `pedido_validar`, `pedido_mensaje`, `pedido_guardar`, `menu.php` | La mesa del cliente. |
| `pedido_errores` | `array<string,string>` | `ctrl_pedido_enviar` | `ctrl_pedido` (y luego se borra) | Errores de validación para mostrar junto a cada campo. |
| `pedido_datos` | `array` | `ctrl_pedido_enviar` | `ctrl_pedido` (y luego se borra) | Datos ya escritos, para no obligar a escribirlos otra vez. |
| `flash` | `array{mensaje,tipo}` | `flash()` | `flash()` (se borra al leer) | Mensaje de una sola vez para la siguiente página. |
| `csrf` | `string` (32 caracteres hexadecimales) | `csrf_token()` | `csrf_token()`, `csrf_verificar()` | Token contra ataques CSRF. |
| `admin` | `bool` | `ctrl_admin_login` | `es_admin()`, `ctrl_admin_salir` (la borra) | Indica si la sesión tiene acceso al panel. |
| `producto_viejo` | `array` | `ctrl_admin_producto_guardar` (si falla) | `ctrl_admin_producto_form` (y luego se borra) | Restaura lo escrito en el formulario de producto tras un error. |

### 2.2 Variables de entrada (`$_GET`, `$_POST`, `$_FILES`)

| Variable | Dónde se lee | Qué contiene |
|---|---|---|
| `$_GET['r']` | `public/index.php` | Nombre de la página, por ejemplo `menu` o `admin/pedidos`. Por defecto `inicio`. |
| `$_GET['mesa']` | `public/index.php` | Nombre de la mesa al escanear el QR, por ejemplo `Mesa 5`. |
| `$_GET['q']` | `ctrl_menu` | Texto del buscador. |
| `$_GET['cat']` | `ctrl_menu` | Categoría elegida en el filtro. |
| `$_GET['estado']` | `ctrl_admin_pedidos` | Estado para filtrar pedidos. |
| `$_GET['id']` | `ctrl_admin_producto_form`, `ctrl_admin_pedido` | Id del producto o del pedido. |
| `$_POST['_csrf']` | `csrf_verificar` | Token que debe coincidir con `$_SESSION['csrf']`. |
| `$_POST['accion']` | `ctrl_carrito_accion` | `agregar`, `cambiar`, `quitar` o `vaciar`. |
| `$_POST['id']` | `ctrl_carrito_accion`, y controladores de productos, categorías, pedidos | Id del registro afectado. |
| `$_POST['cantidad']` | `ctrl_carrito_accion`, `ctrl_admin_mesas_generar` | Cantidad nueva en el carrito, o número de mesas a generar. |
| `$_POST['volver']` | `ctrl_carrito_accion` | A qué página regresar: `menu`, `inicio` o carrito por defecto. |
| `$_POST['mesa']` | `ctrl_mesa_elegir` | Mesa elegida en el menú (vacío = para llevar). |
| `$_POST['nombre']`, `telefono`, `entrega`, `direccion`, `barrio`, `pago`, `nota` | `pedido_validar` (vía `$entrada`) | Datos del formulario de pedido. |
| `$_POST['clave']` | `ctrl_admin_login` | Contraseña del panel. |
| `$_POST['mesas']` | `ctrl_admin_mesas_guardar` | Texto con una mesa por línea. |
| `$_POST['whatsapp']`, `domicilio_costo`, `mensaje_pedido` | `ctrl_admin_whatsapp_guardar` | Ajustes de WhatsApp. |
| `$_POST['negocio_nombre']`, `sobre_nosotros`, `direccion`, `horario`, `telefono`, `correo` | `ctrl_admin_negocio_guardar` | Datos del negocio. |
| `$_POST['estado']` | `ctrl_admin_pedido_estado` | Nuevo estado del pedido. |
| `$_POST['direccion']` | `ctrl_admin_producto_mover` | `subir` o `bajar`. |
| `$_FILES['imagen']` | `ctrl_admin_producto_guardar` → `imagen_guardar` | Archivo subido: `tmp_name`, `size`, `error`. |

### 2.3 Variables de configuración (`config/config.php`)

Se leen con `config()`, definida en `src/bootstrap.php`. Solo se carga una vez por petición (estática).

| Clave | Valor inicial | Dónde se usa | Para qué |
|---|---|---|---|
| `db_path` | `data/lupa.sqlite` | `db()` | Ruta del archivo de la base. |
| `uploads_dir` | `public/uploads` | `imagen_guardar`, `imagen_borrar` | Carpeta de fotos subidas. |
| `max_upload_bytes` | 2 MB | `imagen_guardar` | Tamaño máximo de la foto. |
| `zona_horaria` | `America/Bogota` | `bootstrap.php` | Zona horaria de `date()` (fechas de pedidos). |
| `sesion_nombre` | `lupa_sess` | `bootstrap.php` | Nombre de la cookie de sesión. |
| `cantidad_maxima` | 99 | `carrito_agregar`, `carrito_cambiar_cantidad` | Límite de unidades por producto. |
| `ajustes` | arreglo | `ajuste()` | Valores por defecto de los datos del negocio y del mensaje. |
| `productos_iniciales` | 8 productos | `bin/setup.php` | Menú de ejemplo al crear la base. |
| `mesas_iniciales` | 12 mesas | `bin/setup.php` | Mesas de ejemplo al crear la base. |

**Variable de entorno:** `LUPA_DB_PATH`. Si existe, `config()` cambia `db_path` por esa ruta. Solo la usan las pruebas (`tests/run.php`) para trabajar con una base temporal.

### 2.4 Ajustes guardados en la base (`ajustes`)

| Clave | Quién la guarda | Quién la lee |
|---|---|---|
| `negocio_nombre`, `direccion`, `horario`, `telefono`, `correo`, `sobre_nosotros` | `ctrl_admin_negocio_guardar` | `ajuste()` en el pie, Información y el logo |
| `whatsapp`, `domicilio_costo`, `mensaje_pedido` | `ctrl_admin_whatsapp_guardar` | `ctrl_pedido`, `pedido_mensaje`, `whatsapp_url`, el layout |
| `admin_hash` | `bin/setup.php` | `ctrl_admin_login` |

---

## 3. Constantes

| Constante | Archivo y línea | Valor | Uso |
|---|---|---|---|
| `ENTREGAS` | `src/pedidos.php` línea 4 | `domicilio`, `recoger`, `mesa` → sus nombres en español | Valida la forma de entrega y arma el texto del mensaje. |
| `PAGOS` | `src/pedidos.php` línea 10 | `Efectivo`, `Nequi`, `Transferencia` | Valida el método de pago. |
| `ESTADOS_PEDIDO` | `src/pedidos.php` línea 114 | `pendiente`, `confirmado`, `entregado`, `cancelado` → nombres en español | Valida el cambio de estado y se usa en el filtro y el CSV. |

---

## 4. Funciones, archivo por archivo

Cada tabla indica la **línea** de la definición, los **parámetros** y el **retorno**, lo que **hace** y **quién la llama**.

### 4.1 `public/index.php` · punto de entrada

Este archivo no define funciones. Sus pasos son:

1. Carga `src/bootstrap.php`.
2. Si llega `?mesa=`, llama a `mesa_fijar()`. Si no llega `r`, redirige al menú.
3. Toma `r` y el método HTTP y llama a `despachar()`.
4. Si ocurre cualquier excepción (`Throwable`), responde 500 con un mensaje genérico y lo registra con `error_log()`.

### 4.2 `src/bootstrap.php` · arranque

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `config()` | 9 | `()` → `array` | Carga `config/config.php` una vez y aplica `LUPA_DB_PATH` si existe. | Casi todos los archivos. |

Lo que no es función pero se ejecuta al cargar el archivo:

- `date_default_timezone_set()` con la zona horaria de la configuración.
- Si no es CLI: `session_name()`, `session_set_cookie_params()` (con `httponly` y `SameSite=Lax`) y `session_start()`.
- Carga, en este orden: `util.php`, `db.php`, `tienda.php`, `carrito.php`, `pedidos.php`, `ctrl_publico.php`, `ctrl_admin.php` y `rutas.php`.

### 4.3 `src/util.php` · utilidades comunes

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `e()` | 5 | `mixed` → `string` | Escapa HTML con `htmlspecialchars` (comillas incluidas, UTF-8). | Todas las vistas (146 usos). |
| `dinero()` | 11 | `int` → `string` | Formatea pesos: `8000` → `$8.000`. | Vistas, `pedido_lineas`, `pedido_mensaje`, `carrito`. |
| `url()` | 17 | `string $ruta, array $extra` → `string` | Arma `index.php?r=…` con parámetros extra. Deja las barras `/` sin codificar. | Casi todas las vistas y controladores (82 usos). |
| `url_absoluta()` | 25 | `array $params` → `string` | Arma la URL completa con `http` o `https` y el host actual. Se usa en los QR. | `ctrl_admin_mesas`, `mesas.php`. |
| `redirigir()` | 34 | `string $destino` → `never` | Envía el encabezado `Location` y termina el script. | Todos los controladores. |
| `flash()` | 41 | `?string $mensaje, string $tipo` → `?array` | Si recibe texto, lo guarda en la sesión. Si no, lo devuelve y lo borra. | Controladores y el layout. |
| `csrf_token()` | 52 | `()` → `string` | Devuelve el token de la sesión; si no existe, crea uno de 32 caracteres. | `csrf_campo`. |
| `csrf_campo()` | 60 | `()` → `string` | Devuelve un `<input type="hidden" name="_csrf">` con el token. | Todos los formularios `POST` (21 usos). |
| `csrf_verificar()` | 66 | `()` → `void` | Compara el token de `$_POST['_csrf']` con la sesión usando `hash_equals`. Si no coincide, responde 419 y termina. | Todos los controladores `POST` (17 usos). |
| `render()` | 77 | `string $__vista, array $__datos, string $__layout` → `void` | Convierte `$__datos` en variables con `extract`, renderiza la vista en un búfer, y luego incluye el layout con `$contenido`. | Los controladores de página. |
| `es_admin()` | 88 | `()` → `bool` | Indica si la sesión tiene `admin` verdadero. | `requerir_admin`, `ctrl_admin_entrar`, `admin.php`. |
| `requerir_admin()` | 94 | `()` → `void` | Si no es admin, guarda un aviso y redirige al login. | Todas las acciones del panel (21 usos). |

### 4.4 `src/db.php` · base de datos y ajustes

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `db()` | 5 | `()` → `PDO` | Abre la conexión SQLite una sola vez (estática), activa las claves foráneas, crea la carpeta de la base y llama a `instalar_esquema()`. Errores como excepciones. | Todas las funciones que consultan (28 usos). |
| `instalar_esquema()` | 26 | `PDO $pdo` → `void` | Crea las tablas `categorias`, `productos`, `mesas`, `pedidos` y `ajustes` si no existen (`CREATE TABLE IF NOT EXISTS`). | `db()`. |
| `ajuste()` | 77 | `string $clave` → `string` | Lee un ajuste de la tabla. Si no existe, devuelve el valor por defecto de `config()`. | Vistas y funciones de pedidos (32 usos). |
| `ajustes_guardar()` | 89 | `array $valores` → `void` | Guarda varios ajustes con `INSERT … ON CONFLICT DO UPDATE` (crea o actualiza). | `ctrl_admin_whatsapp_guardar`, `ctrl_admin_negocio_guardar`, `bin/setup.php`. |

### 4.5 `src/tienda.php` · categorías, productos, mesas e imágenes

**Categorías**

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `categorias_listar()` | 6 | `()` → `array` | Lista las categorías con el número de productos de cada una (`total`). | `ctrl_admin_categorias`, `ctrl_admin_producto_form`. |
| `categoria_crear()` | 14 | `string $nombre` → `void` | Valida que no esté vacío, asigna el siguiente `orden` e inserta. Si el nombre ya existe (por ser `UNIQUE`), lanza un error claro. | `ctrl_admin_categoria_guardar`. |
| `categoria_eliminar()` | 28 | `int $id` → `void` | **Se niega** si tiene productos; si no, borra la categoría. | `ctrl_admin_categoria_eliminar`. |

**Productos**

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `productos_listar()` | 40 | `bool $soloDisponibles = false` → `array` | Lista productos con su categoría, ordenados por `orden`. Con `true` solo trae los disponibles. | `ctrl_inicio`, `ctrl_menu`, `ctrl_admin_productos`. |
| `producto_obtener()` | 51 | `int $id` → `?array` | Devuelve un producto por id o `null`. | `carrito_agregar`, `carrito_items`, `producto_guardar`, `producto_eliminar`, `ctrl_admin_producto_form`. |
| `producto_guardar()` | 63 | `array $datos, ?int $id, ?string $imagen` → `int` | Valida nombre, precio (quita puntos con `preg_replace`) y etiqueta. Si `$id` es nulo inserta con el siguiente `orden`; si no, actualiza. En actualización, `$imagen` nulo conserva la foto anterior y, si cambia, **borra la foto vieja**. Devuelve el id. | `ctrl_admin_producto_guardar`. |
| `producto_alternar_disponible()` | 109 | `int $id` → `void` | Cambia `disponible` entre 0 y 1 con `1 - disponible`. | `ctrl_admin_producto_disponible`. |
| `producto_eliminar()` | 114 | `int $id` → `void` | Borra el producto y **también borra su foto** con `imagen_borrar()`. Los pedidos guardados no se afectan porque guardan sus líneas en JSON. | `ctrl_admin_producto_eliminar`. |
| `producto_mover()` | 125 | `int $id, string $direccion` → `void` | Busca la posición del producto en el orden, intercambia con su vecino (`subir` o `bajar`) y renumera todos de 10 en 10 dentro de una transacción. Si está en el extremo, no hace nada. | `ctrl_admin_producto_mover`. |
| `productos_filtrar()` | 236 | `array $productos, string $texto, string $categoria` → `array` | Filtra en memoria por categoría y por texto dentro de nombre y descripción, sin distinguir mayúsculas. | `ctrl_menu`. |

**Fotos**

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `imagen_guardar()` | 148 | `array $archivo` → `?string` | Si no se subió nada, devuelve `null`. Valida el error de subida, el tamaño (2 MB) y el tipo real con `getimagesize()`. Guarda con un nombre aleatorio y devuelve `uploads/…`. | `ctrl_admin_producto_guardar`. |
| `imagen_borrar()` | 178 | `string $ruta` → `void` | Borra un archivo de `public/uploads/`. Ignora rutas que no empiecen por `uploads/` (protección). | `producto_guardar`, `producto_eliminar`, `ctrl_admin_producto_guardar`. |

**Mesas**

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `mesas_listar()` | 191 | `()` → `array` | Lista las mesas en orden. | `ctrl_menu`, `ctrl_mesa_elegir`, `ctrl_admin_mesas`. |
| `mesas_reemplazar()` | 197 | `string $texto` → `int` | Separa por líneas, recorta a 40 caracteres, quita duplicados y vacías. Dentro de una transacción borra **todas** las mesas e inserta la lista nueva. Devuelve cuántas quedaron. | `ctrl_admin_mesas_guardar`. |
| `mesas_generar()` | 220 | `int $n` → `int` | Crea "Mesa 1" a "Mesa N" con `INSERT OR IGNORE`, así no duplica las existentes. Valida 1 a 100. | `ctrl_admin_mesas_generar`. |

### 4.6 `src/carrito.php` · carrito y mesa

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `carrito_agregar()` | 10 | `int $id` → `void` | Verifica que el producto exista y esté disponible. Suma 1 a la cantidad, sin pasar de 99. Guarda un aviso con el nombre. | `ctrl_carrito_accion`. |
| `carrito_cambiar_cantidad()` | 22 | `int $id, int $cantidad` → `void` | Si la cantidad es 0 o menos, quita el producto. Si está en el carrito, la cambia (máximo 99). | `ctrl_carrito_accion`. |
| `carrito_quitar()` | 34 | `int $id` → `void` | Borra el producto de la sesión. | `ctrl_carrito_accion`, `carrito_cambiar_cantidad`. |
| `carrito_vaciar()` | 39 | `()` → `void` | Deja el carrito como arreglo vacío. | `ctrl_carrito_accion`. |
| `carrito_items()` | 48 | `()` → `array` | Consulta en **una sola** consulta los productos del carrito (con `IN (?,?,…)`), arma las líneas (`producto`, `cantidad`, `linea`), y **quita de la sesión** los agotados o eliminados con un aviso. Tiene efectos secundarios, por eso debe leerse con cuidado. | `ctrl_carrito`, `ctrl_pedido`, `ctrl_pedido_enviar`. |
| `carrito_totales()` | 93 | `array $items` → `array{subtotal,unidades}` | Suma los importes de línea y las unidades. | `ctrl_carrito`, `ctrl_pedido`, `pedido_totales`, `pedido_mensaje`. |
| `carrito_unidades()` | 105 | `()` → `int` | Suma las cantidades de la sesión, sin consultar la base. Lo usa el icono del carrito. | Layout público. |
| `mesa_actual()` | 111 | `()` → `?string` | Devuelve la mesa de la sesión o `null`. | `pedido_validar`, `pedido_mensaje`, `pedido_guardar`, `menu.php`. |
| `mesa_fijar()` | 116 | `string $nombre` → `void` | Guarda la mesa en la sesión, recortada a 40 caracteres. | `public/index.php`, `ctrl_mesa_elegir`. |
| `mesa_limpiar()` | 122 | `()` → `void` | Quita la mesa de la sesión. | `ctrl_mesa_elegir`. |

### 4.7 `src/pedidos.php` · pedidos y mensaje de WhatsApp

| Función | Línea | Parámetros → retorno | Qué hace | La llaman |
|---|---|---|---|---|
| `pedido_validar()` | 16 | `array $entrada` → `array{errores,datos}` | Limpia y recorta cada campo con `mb_substr`, y valida: nombre (2 o más), teléfono (7 dígitos o más), entrega (en `ENTREGAS`), mesa (si la entrega es "mesa", debe haber una mesa en la sesión), pago (en `PAGOS`), y si es domicilio, dirección (5 o más) y barrio (2 o más). Devuelve errores por campo y datos limpios. | `ctrl_pedido_enviar`. |
| `pedido_domicilio()` | 58 | `string $entrega` → `int` | Devuelve el costo del domicilio solo si la entrega es domicilio. | `pedido_totales`, `pedido_mensaje`. |
| `pedido_lineas()` | 64 | `array $items` → `string` | Arma una línea por producto: `• 2 x Nombre — $19.000`. | `pedido_mensaje`. |
| `pedido_mensaje()` | 77 | `string $plantilla, array $datos, array $items` → `string` | Reemplaza las llaves `{negocio}`, `{pedido}`, `{total}`… con `strtr`. Es la misma función que usa la vista previa del panel. | `ctrl_pedido_enviar`, `vista_mensaje`. |
| `whatsapp_url()` | 106 | `string $numero, string $mensaje` → `string` | Deja solo dígitos en el número y codifica el mensaje con `rawurlencode`. Devuelve `https://wa.me/…?text=…`. | `ctrl_pedido_enviar`. |
| `pedido_totales()` | 122 | `array $datos, array $items` → `array{subtotal,domicilio,total}` | Calcula los tres importes del pedido, con la misma regla que el mensaje. | `pedido_guardar`. |
| `pedido_guardar()` | 130 | `array $datos, array $items` → `int` | Inserta el pedido con estado `pendiente`. Las líneas se guardan como JSON. Devuelve el número del pedido. Se llama **antes** de abrir WhatsApp. | `ctrl_pedido_enviar`. |
| `pedidos_listar()` | 165 | `string $estado = ''` → `array` | Devuelve los últimos 300 pedidos, filtrados por estado si el estado es válido. | `ctrl_admin_pedidos`. |
| `pedido_obtener()` | 175 | `int $id` → `?array` | Devuelve un pedido con sus líneas ya decodificadas en `lineas`. | `ctrl_admin_pedido`. |
| `pedido_cambiar_estado()` | 187 | `int $id, string $estado` → `void` | Valida el estado contra `ESTADOS_PEDIDO` y actualiza. **No comprueba que el pedido exista** (ver sección 7). | `ctrl_admin_pedido_estado`. |
| `pedidos_resumen_hoy()` | 196 | `()` → `array{pedidos,total,pendientes}` | Cuenta los pedidos de hoy y suma sus totales, excluyendo los cancelados. Cuenta los pendientes de todos los días. | `ctrl_admin_pedidos`. |
| `csv_celda()` | 214 | `mixed $valor` → `string` | Escapa una celda para CSV: comillas dobles y protección contra fórmulas (`=`, `+`, `-`, `@`). | `pedidos_csv`. |
| `pedidos_csv()` | 224 | `()` → `string` | Arma el CSV completo: BOM UTF-8, separador `;`, cabecera y una fila por pedido. | `ctrl_admin_pedidos_csv`. |

### 4.8 `src/ctrl_publico.php` · controladores del cliente

| Función | Línea | Qué hace | Ruta | Vista que renderiza |
|---|---|---|---|---|
| `ctrl_inicio()` | 6 | Toma los primeros 4 productos disponibles como "Lo más pedido". | `GET inicio` | `publico/inicio` |
| `ctrl_menu()` | 13 | Lee `q` y `cat`, filtra con `productos_filtrar`, agrupa por categoría (sin categoría va a "Otros") y prepara las mesas. | `GET menu` | `publico/menu` |
| `ctrl_mesa_elegir()` | 42 | Verifica CSRF. Si la mesa está vacía, la quita de la sesión; si existe, la fija. Avisa con `flash` y redirige al menú. | `POST mesa` | (redirige) |
| `ctrl_info()` | 61 | Muestra la página de Información. | `GET info` | `publico/info` |
| `ctrl_carrito()` | 68 | Lee el carrito con `carrito_items` y calcula totales. | `GET carrito` | `publico/carrito` |
| `ctrl_carrito_accion()` | 77 | Verifica CSRF y ejecuta la acción (`match`). Vuelve a una página permitida con `volver`. | `POST carrito` | (redirige) |
| `ctrl_pedido()` | 100 | Si el carrito está vacío, redirige. Si no, lee los errores y datos guardados en sesión (y los borra) y muestra el formulario. Lee el costo del domicilio. | `GET pedido` | `publico/pedido` |
| `ctrl_pedido_enviar()` | 122 | Verifica CSRF, valida, comprueba que haya número de WhatsApp, **guarda el pedido**, arma el mensaje y redirige a `wa.me`. | `POST pedido` | (redirige) |
| `ctrl_no_encontrado()` | 151 | Responde 404 con la página de error. | (cuando la ruta no existe) | `publico/404` |

### 4.9 `src/ctrl_admin.php` · controladores del panel

| Función | Línea | Qué hace | Ruta | Vista |
|---|---|---|---|---|
| `admin_datos()` | 6 | Combina `seccion` con datos extra. Así el layout sabe qué pestaña marcar. | — | — |
| `ctrl_admin_entrar()` | 11 | Si ya es admin, va a productos. Si no, muestra el login. | `GET admin` | `admin/login` |
| `ctrl_admin_login()` | 19 | Verifica CSRF y `password_verify` contra `admin_hash`. Regenera el ID de sesión y marca `admin`. | `POST admin/login` | (redirige) |
| `ctrl_admin_salir()` | 34 | Verifica CSRF, quita `admin` de la sesión y avisa "Sesión cerrada.". | `POST admin/salir` | (redirige) |
| `ctrl_admin_productos()` | 44 | Lista todos los productos (sin filtrar). | `GET admin/productos` | `admin/productos` |
| `ctrl_admin_producto_form()` | 53 | Carga el producto (si hay id), restaura lo escrito tras un error (`producto_viejo`) y lista las categorías. | `GET admin/producto` | `admin/producto_form` |
| `ctrl_admin_producto_guardar()` | 77 | Verifica CSRF. Primero sube la foto, después guarda el producto. Si algo falla, **borra la foto recién subida** para no dejar archivos huérfanos, guarda lo escrito y vuelve al formulario. | `POST admin/producto` | (redirige) |
| `ctrl_admin_producto_mover()` | 100 | Verifica CSRF y llama a `producto_mover`. | `POST admin/producto/mover` | (redirige) |
| `ctrl_admin_producto_disponible()` | 108 | Verifica CSRF y alterna la disponibilidad. | `POST admin/producto/disponible` | (redirige) |
| `ctrl_admin_producto_eliminar()` | 116 | Verifica CSRF y elimina el producto con su foto. | `POST admin/producto/eliminar` | (redirige) |
| `ctrl_admin_categorias()` | 127 | Lista las categorías con su conteo. | `GET admin/categorias` | `admin/categorias` |
| `ctrl_admin_categoria_guardar()` | 136 | Verifica CSRF y crea la categoría. Si hay error, lo muestra. | `POST admin/categorias` | (redirige) |
| `ctrl_admin_categoria_eliminar()` | 149 | Verifica CSRF y borra la categoría si está vacía. | `POST admin/categorias/eliminar` | (redirige) |
| `ctrl_admin_mesas()` | 164 | Lista las mesas con su enlace de QR. | `GET admin/mesas` | `admin/mesas` |
| `ctrl_admin_mesas_guardar()` | 179 | Verifica CSRF y reemplaza la lista de mesas. | `POST admin/mesas` | (redirige) |
| `ctrl_admin_mesas_generar()` | 192 | Verifica CSRF y genera mesas numeradas. | `POST admin/mesas/generar` | (redirige) |
| `ctrl_admin_whatsapp()` | 207 | Muestra la configuración de WhatsApp y la vista previa. | `GET admin/whatsapp` | `admin/whatsapp` |
| `ctrl_admin_whatsapp_guardar()` | 216 | Verifica CSRF. Deja solo dígitos del número y exige 10 o más, convierte el costo a entero (vacío = 0) y exige mensaje no vacío. Guarda los tres ajustes. | `POST admin/whatsapp` | (redirige) |
| `vista_mensaje()` | 246 | Arma un pedido de ejemplo y devuelve el mensaje con `pedido_mensaje`. | — | — |
| `ctrl_admin_negocio()` | 266 | Muestra los datos del negocio. | `GET admin/negocio` | `admin/negocio` |
| `ctrl_admin_negocio_guardar()` | 274 | Verifica CSRF, exige nombre, valida el correo si se escribió y guarda los datos con los límites de longitud. | `POST admin/negocio` | (redirige) |
| `ctrl_admin_pedidos()` | 304 | Lista pedidos con filtro, resumen del día y estado seleccionado. | `GET admin/pedidos` | `admin/pedidos` |
| `ctrl_admin_pedido()` | 316 | Muestra el detalle de un pedido o redirige si no existe. | `GET admin/pedido` | `admin/pedido` |
| `ctrl_admin_pedido_estado()` | 330 | Verifica CSRF y cambia el estado. Captura `InvalidArgumentException` y muestra el error. | `POST admin/pedido/estado` | (redirige) |
| `ctrl_admin_pedidos_csv()` | 344 | Envía el CSV como descarga con cabeceras `Content-Type` y `Content-Disposition`. | `GET admin/pedidos/csv` | (descarga) |

### 4.10 `src/rutas.php` · tabla de rutas

| Función | Línea | Parámetros → retorno | Qué hace | La llama |
|---|---|---|---|---|
| `despachar()` | 8 | `string $ruta, string $metodo` → `void` | Arma la clave `"MÉTODO ruta"`, la busca en un arreglo asociativo y llama al controlador. Si no existe, llama a `ctrl_no_encontrado()`. | `public/index.php`. |

La tabla tiene **31 rutas** en total: **23 del panel** y **8 públicas**. Se pueden ver completas en el archivo.

### 4.11 `bin/setup.php` · preparación

No define funciones. Lee `--clave` y `--reiniciar` con `getopt()`, crea la base, inserta el menú y las mesas de `config()`, y guarda el hash de la contraseña con `ajustes_guardar()`.

### 4.12 `bin/iniciar.sh`, `bin/verificar.sh`

Scripts de shell. `iniciar.sh` revisa PHP, crea la base la primera vez y ejecuta `php -S`. `verificar.sh` revisa la sintaxis de todos los `.php` con `php -l` y corre `tests/run.php`.

---

## 5. Variables de las vistas

Las vistas no llaman a la base: reciben datos desde el controlador por `render()`, que los convierte en variables.

**Layouts**

| Archivo | Variables que usa |
|---|---|
| `layouts/publico.php` | `$titulo`, `$contenido` (HTML ya generado), `$seccion` (pestaña activa), `$aviso` (flash), `$nombreNegocio`, `$whatsapp` (solo dígitos) |
| `layouts/admin.php` | `$titulo`, `$contenido`, `$seccion`, `$secciones` (pestañas), `$logueado`, `$aviso` |

`$contenido` no se escapa en el layout porque cada vista ya escapa su salida con `e()`.

**Vistas públicas**

| Vista | Variables principales |
|---|---|
| `publico/inicio.php` | `$destacados` (productos) |
| `publico/menu.php` | `$grupos` (productos por categoría), `$categorias`, `$texto`, `$categoria`, `$total`, `$mesas` |
| `publico/carrito.php` | `$items`, `$totales` |
| `publico/pedido.php` | `$items`, `$totales`, `$errores`, `$datos`, `$mesa`, `$costoDomicilio` |
| `publico/info.php` | Ajustes del negocio con `ajuste()` |
| `parciales/producto_tarjeta.php` | `$producto`, `$seccion` (a dónde volver) |

**Vistas del panel**

| Vista | Variables principales |
|---|---|
| `admin/login.php` | (ninguna) |
| `admin/productos.php` | `$productos` |
| `admin/producto_form.php` | `$producto` (puede venir vacío o con valores de `producto_viejo`), `$categorias`, `$editando` |
| `admin/categorias.php` | `$categorias` (con `total`) |
| `admin/mesas.php` | `$mesas` (con `enlace`) |
| `admin/whatsapp.php` | `$previa` (texto de la vista previa) |
| `admin/negocio.php` | Ajustes con `ajuste()` |
| `admin/pedidos.php` | `$pedidos`, `$resumen`, `$estado` |
| `admin/pedido.php` | `$pedido` (con `lineas`) |

---

## 6. Trazas: cómo se ejecutan las acciones principales

### 6.1 Agregar un producto al carrito

1. Botón en `parciales/producto_tarjeta.php` → formulario `POST` a `index.php?r=carrito` con `accion=agregar`, `id`, `volver` y `_csrf`.
2. `public/index.php` → `despachar('carrito', 'POST')` (`rutas.php`).
3. `ctrl_carrito_accion()` → `csrf_verificar()` → `match` → `carrito_agregar($id)`.
4. `carrito_agregar()` → `producto_obtener($id)` (consulta) → si está disponible, suma en `$_SESSION['carrito']` y guarda un `flash`.
5. `ctrl_carrito_accion()` decide la página de regreso y llama a `redirigir()`.
6. El navegador carga el menú: `ctrl_menu()` → `productos_listar(true)` → `productos_filtrar()` → `render('publico/menu')` → layout con `carrito_unidades()` en el icono.

### 6.2 Enviar un pedido

1. `ctrl_pedido()` muestra el formulario con `$_SESSION['pedido_errores']` (si hubo errores).
2. El formulario envía a `index.php?r=pedido` por `POST`.
3. `ctrl_pedido_enviar()` → `csrf_verificar()` → `carrito_items()` (si está vacío, redirige).
4. `pedido_validar($_POST)` devuelve `[$errores, $datos]`.
   - Si hay errores: se guardan en la sesión (`pedido_errores`, `pedido_datos`), se avisa y se redirige a `pedido`.
5. Se lee `ajuste('whatsapp')`. Si está vacío, avisa y regresa.
6. `pedido_guardar($datos, $items)` → `pedido_totales()` → `carrito_totales()` (dentro) → INSERT con `ENTREGAS`.
7. `pedido_mensaje(ajuste('mensaje_pedido'), $datos, $items)` → `pedido_lineas()`, `pedido_domicilio()`, `mesa_actual()`, `strtr`.
8. `whatsapp_url($numero, $mensaje)` → `redirigir()` a `https://wa.me/…`.
9. El carrito **no se vacía**: quedó en la sesión por si WhatsApp no abre.

### 6.3 Guardar un producto con foto

1. `ctrl_admin_producto_guardar()` → `requerir_admin()` → `csrf_verificar()`.
2. `imagen_guardar($_FILES['imagen'])` → valida tamaño y tipo con `getimagesize()` → mueve el archivo a `public/uploads/` con nombre aleatorio → devuelve `uploads/…` o `null` si no hay foto.
3. `producto_guardar($_POST, $id, $imagen)` → valida → INSERT o UPDATE → si cambió la foto, `imagen_borrar()` borra la anterior.
4. Si todo va bien: `flash` y redirige a `admin/productos`.
5. Si algo falla (`InvalidArgumentException` o `RuntimeException`): se borra la foto recién subida (`imagen_borrar`), se guarda lo escrito en `producto_viejo` y se vuelve al formulario.

### 6.4 Entrar al panel

1. `GET admin` → `ctrl_admin_entrar()` → muestra `admin/login`.
2. `POST admin/login` → `ctrl_admin_login()` → `csrf_verificar()` → `ajuste('admin_hash')` → `password_verify()`.
3. Si coincide: `session_regenerate_id(true)`, `$_SESSION['admin'] = true` y redirige a productos.
4. Cada acción del panel empieza con `requerir_admin()`, que comprueba `es_admin()`.

### 6.5 Cambiar el estado de un pedido

1. `GET admin/pedido?id=N` → `ctrl_admin_pedido()` → `pedido_obtener()` → `render('admin/pedido')`.
2. `POST admin/pedido/estado` → `ctrl_admin_pedido_estado()` → `requerir_admin()` → `csrf_verificar()` → `pedido_cambiar_estado()`.
3. Si el estado no es válido, `InvalidArgumentException` → `flash` con el error.
4. Si es válido, `flash('Estado actualizado.')` y redirige a `admin/pedido?id=N`.

### 6.6 Descargar el CSV

`GET admin/pedidos/csv` → `ctrl_admin_pedidos_csv()` → `requerir_admin()` → cabeceras de descarga → `pedidos_csv()` → por cada pedido, `csv_celda()` en cada celda → `exit`.

---

## 7. Observaciones sobre el código

Estas notas son útiles para mostrar que se revisó el código con criterio. Cada una tiene una mejora posible.

1. **`pedido_cambiar_estado()` no comprueba que el pedido exista.** Un `UPDATE` sobre un id inexistente no falla, así que el panel dice "Estado actualizado." aunque no haya cambiado nada. *Mejora:* revisar el número de filas afectadas o consultar antes.
2. **`carrito_items()` tiene efectos secundarios.** Lee productos y además modifica la sesión y agrega avisos. Es cómodo, pero una función que "solo lee" no debería escribir. *Mejora:* separar la lectura de la limpieza del carrito.
3. **`mesas_reemplazar()` borra todas las mesas.** Una mesa quitada de la lista pierde su QR. *Mejora:* borrar solo las que no están en la lista nueva.
4. **`ctrl_admin_producto_guardar()` solo captura dos tipos de error.** Un error de base de datos (`PDOException`) llega al manejador general de `index.php`, que muestra una pantalla de error genérica. Es seguro, pero el administrador pierde lo que escribió.
5. **`render()` usa `extract()`.** Las claves de `$__datos` se convierten en variables. Los nombres con `__` evitan choques con las variables internas de la función. Es un patrón común en PHP sin framework, pero dificulta seguir el origen de cada variable.
6. **`ajuste()` consulta la base cada vez que se llama** (32 usos). Para este proyecto no molesta, pero en un sitio con mucho tráfico se podría cargar todos los ajustes una sola vez por petición.
7. **Un solo token CSRF por sesión.** Es suficiente para este proyecto. Un token por formulario sería más estricto.
8. **La sesión de administrador no caduca por tiempo.** Solo se cierra con "Salir" o al cerrar el navegador por completo. *Mejora:* un tiempo máximo de inactividad.
9. **No hay límite de intentos de login** (`ctrl_admin_login()`). Ya está mencionado en la guía.
10. **`pedido_validar()` no revisa `direccion` ni `barrio` cuando la entrega no es domicilio**, lo cual es correcto: se borran al guardar (`pedido_guardar` guarda vacío).

---

## 8. Pruebas automáticas (`tests/run.php`)

El archivo tiene **50 comprobaciones** (`afirmar()`) agrupadas en 16 bloques. Se ejecutan con `php tests/run.php` o con `bin/verificar.sh`. Usan una base temporal (`LUPA_DB_PATH`).

| Bloque | Qué verifica |
|---|---|
| 1 · Formato de dinero | `dinero()` pone punto de miles. |
| 2 · Escape HTML | `e()` escapa `<`, `>` y comillas. |
| 3 · Precio y validaciones de producto | Un precio con puntos se guarda como entero. |
| 4 · Orden del menú | Subir y bajar productos, incluso en los extremos. |
| 5 · Carrito | Sumar, límite de 99 y quitar. |
| 6 · Totales | Subtotal y unidades. |
| 7 · Producto agotado | Un producto que se agota sale del carrito. |
| 8 · Validación del pedido | Nombre, teléfono, entrega, pago, domicilio y mesa. |
| 9 · Mensaje de WhatsApp | Plantilla completa, sin llaves sin reemplazar, con la nota y la mesa. |
| 10 · Enlace de WhatsApp | Número solo con dígitos y mensaje codificado. |
| 11 · Mesas | Sin duplicados ni líneas vacías. |
| 12 · Categorías | No se puede borrar una categoría con productos. |
| 13 · Ruta de QR | El enlace apunta a `index.php` con `mesa`. |
| 14 · Filtro del menú | Búsqueda por texto y por categoría. |
| 15 · Pedidos guardados | Estados, CSV con BOM y protección de fórmulas. |
| 16 · Marcado del menú | Cada `<section>` se cierra (prueba del error corregido). |

---

## 9. JavaScript y CSS

### 9.1 `public/assets/app.js`

Todo el código está dentro de una función anónima autoejecutable (IIFE) con `'use strict'`, para no crear variables globales. Bloques:

| Bloque | Variables y funciones | Qué hace |
|---|---|---|
| Volver arriba | `subir`, `mostrar` | Muestra el botón después de bajar 400 píxeles y desplaza suavemente al inicio. |
| Confirmaciones | `form[data-confirm]` | Pide confirmación antes de borrar o vaciar. |
| Pedido | `pedido`, `bloque`, `resDom`, `resTotal`, `subtotal`, `base`, `pesos`, `actualizar` | Muestra los campos de domicilio y recalcula el total al cambiar la entrega. `pesos()` formatea con puntos. |
| Códigos QR | `window.QRCode`, `canvas[data-qr]` | Dibuja los QR del panel. |

El sitio funciona sin JavaScript para comprar: las funciones de este archivo solo mejoran la experiencia.

### 9.2 `public/assets/style.css`

- **Variables de color y medidas** en `:root`: `--morado`, `--rosa`, `--verde`, `--texto`, `--gris`, `--fondo`, `--linea`, `--radio`, `--sombra`, `--sombra-alta`, `--fuente`, `--fuente-titulo`.
- **Secciones** (en el mismo orden del archivo): botones, avisos, cabecera pública, logo, contenido, inicio, tarjetas de producto, carrito, formularios, pie, panel del administrador, respuesta para móviles (`max-width: 900px` y `700px`), selector de mesa, y movimiento reducido.
- **Movimiento reducido:** `prefers-reduced-motion` desactiva animaciones y elevaciones.

---

## 10. Cómo seguir el código en una sustentación

1. Abre `public/index.php` y explica las tres líneas clave: `require`, `mesa_fijar` y `despachar`.
2. Abre `src/rutas.php` y muestra la tabla: "POST carrito" → `ctrl_carrito_accion`.
3. Abre `src/ctrl_publico.php` en `ctrl_carrito_accion` y explica `csrf_verificar` y `match`.
4. Abre `src/carrito.php` en `carrito_agregar` y explica `producto_obtener`.
5. Abre `src/pedidos.php` en `pedido_validar` y `pedido_guardar`, y muestra la consulta con `?`.
6. Ejecuta `bin/verificar.sh` y muestra `TODO OK`.
