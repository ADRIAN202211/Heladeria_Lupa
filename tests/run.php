<?php
declare(strict_types=1);

/*
 * Pruebas sin dependencias externas: php tests/run.php
 * Usan una base de datos temporal, así nunca tocan data/lupa.sqlite.
 */

$temporal = sys_get_temp_dir() . '/lupa_test_' . getmypid() . '.sqlite';
putenv('LUPA_DB_PATH=' . $temporal);
require __DIR__ . '/../src/bootstrap.php';

$ok = 0;
$fallos = [];

function afirmar(bool $cumple, string $descripcion): void
{
    global $ok, $fallos;
    if ($cumple) {
        $ok++;
        echo '.';
    } else {
        $fallos[] = $descripcion;
        echo 'F';
    }
}

function esperar_error(callable $f, string $descripcion): void
{
    try {
        $f();
        afirmar(false, $descripcion . ' (no lanzó error)');
    } catch (InvalidArgumentException) {
        afirmar(true, $descripcion);
    }
}

// Base limpia con datos del manual.
db();
$cfg = config();
$_SESSION = [];
$st = db()->prepare('INSERT OR IGNORE INTO ajustes (clave, valor) VALUES (?, ?)');
foreach ($cfg['ajustes'] as $k => $v) {
    $st->execute([$k, (string) $v]);
}
categoria_crear('Conos');
categoria_crear('Canastas');
$idCono = (int) db()->query("SELECT id FROM categorias WHERE nombre = 'Conos'")->fetchColumn();
producto_guardar(['nombre' => 'Cono Arequipe', 'precio' => '9.500', 'categoria_id' => $idCono, 'disponible' => '1', 'etiqueta' => 'Favorito'], null, null);
producto_guardar(['nombre' => 'Cono Vainilla', 'precio' => '8000', 'categoria_id' => $idCono, 'disponible' => '1'], null, null);
producto_guardar(['nombre' => 'Canasta Agotada', 'precio' => '10000', 'disponible' => ''], null, null);
$ids = array_map('intval', db()->query('SELECT id FROM productos ORDER BY orden, id')->fetchAll(PDO::FETCH_COLUMN));
[$arequipe, $vainilla, $agotada] = $ids;

// 1. Formato de dinero
afirmar(dinero(8000) === '$8.000', 'dinero() formatea con punto de miles');
afirmar(dinero(1234567) === '$1.234.567', 'dinero() formatea millones');

// 2. Escape HTML
afirmar(e('<b>"x"</b>') === '&lt;b&gt;&quot;x&quot;&lt;/b&gt;', 'e() escapa HTML');

// 3. Precio y validaciones de producto
esperar_error(fn () => producto_guardar(['nombre' => '', 'precio' => '5000'], null, null), 'rechaza producto sin nombre');
esperar_error(fn () => producto_guardar(['nombre' => 'X', 'precio' => '0'], null, null), 'rechaza precio cero');
esperar_error(fn () => producto_guardar(['nombre' => 'X', 'precio' => '5000', 'etiqueta' => 'Raro'], null, null), 'rechaza etiqueta no válida');
afirmar((int) producto_obtener($arequipe)['precio'] === 9500, 'precio con puntos se guarda como 9500');

// 4. Orden del menú: subir y bajar
producto_mover($vainilla, 'subir');
$orden = array_map('intval', db()->query('SELECT id FROM productos ORDER BY orden, id')->fetchAll(PDO::FETCH_COLUMN));
afirmar($orden[0] === $vainilla, 'subir deja el producto en la primera posición');
producto_mover($vainilla, 'subir'); // ya está arriba: no debe romper nada
afirmar((int) db()->query('SELECT COUNT(*) FROM productos')->fetchColumn() === 3, 'subir en la primera posición no altera los datos');
producto_mover($vainilla, 'bajar');
$orden = array_map('intval', db()->query('SELECT id FROM productos ORDER BY orden, id')->fetchAll(PDO::FETCH_COLUMN));
afirmar($orden[1] === $vainilla, 'bajar devuelve el producto a la posición anterior');

// 5. Carrito: sumar, cantidad máxima y quitar
$_SESSION['carrito'] = [];
carrito_agregar($arequipe);
carrito_agregar($arequipe);
afirmar(($_SESSION['carrito'][$arequipe] ?? 0) === 2, 'agregar dos veces suma 2 unidades');
carrito_cambiar_cantidad($arequipe, 500);
afirmar($_SESSION['carrito'][$arequipe] === $cfg['cantidad_maxima'], 'la cantidad no supera el máximo');
carrito_cambiar_cantidad($arequipe, 2);
carrito_agregar($agotada); // no disponible: no debe entrar
afirmar(!isset($_SESSION['carrito'][$agotada]), 'producto agotado no entra al carrito');

// 6. Totales del carrito
$items = carrito_items();
$tot = carrito_totales($items);
afirmar($tot['subtotal'] === 19000, 'subtotal 2 x $9.500 = $19.000');
afirmar($tot['unidades'] === 2, 'unidades suman 2');

// 7. Un producto que se agota después se quita del carrito
db()->prepare('UPDATE productos SET disponible = 0 WHERE id = ?')->execute([$arequipe]);
$items = carrito_items();
afirmar($items === [] && !isset($_SESSION['carrito'][$arequipe]), 'producto que se agota sale del carrito');
db()->prepare('UPDATE productos SET disponible = 1 WHERE id = ?')->execute([$arequipe]);
$_SESSION['carrito'] = [$arequipe => 2];
$items = carrito_items();

// 8. Validación del formulario de pedido
[$err] = pedido_validar(['nombre' => 'A', 'telefono' => '12', 'entrega' => '', 'pago' => '']);
afirmar(isset($err['nombre'], $err['telefono'], $err['entrega'], $err['pago']), 'valida nombre, teléfono, entrega y pago');

[$err] = pedido_validar(['nombre' => 'Laura', 'telefono' => '3001234567', 'entrega' => 'domicilio', 'pago' => 'Efectivo']);
afirmar(isset($err['direccion'], $err['barrio']) && !isset($err['nombre']), 'domicilio exige dirección y barrio');

$_SESSION['mesa'] = null;
unset($_SESSION['mesa']);
[$err] = pedido_validar(['nombre' => 'Laura', 'telefono' => '3001234567', 'entrega' => 'mesa', 'pago' => 'Efectivo']);
afirmar(isset($err['entrega']), '"En mi mesa" sin QR escaneado da error');

mesa_fijar('Mesa 5');
[$err, $limpio] = pedido_validar([
    'nombre' => ' Laura Restrepo ', 'telefono' => '300 123 4567', 'entrega' => 'mesa', 'pago' => 'Nequi', 'nota' => 'Sin crema',
]);
afirmar($err === [] && $limpio['nombre'] === 'Laura Restrepo', 'pedido válido en mesa se limpia y se acepta');

// 9. Mensaje de WhatsApp con la plantilla del manual
$msg = pedido_mensaje(ajuste('mensaje_pedido'), $limpio, $items);
afirmar(str_contains($msg, '2 x Cono Arequipe — $19.000'), 'el mensaje lista el producto con cantidad y línea');
afirmar(str_contains($msg, 'Entrega: En mi mesa (Mesa 5)'), 'el mensaje indica la mesa');
afirmar(str_contains($msg, 'Domicilio: No aplica'), 'sin domicilio aparece "No aplica"');
afirmar(!str_contains($msg, '{'), 'no quedan llaves sin reemplazar');
afirmar(str_contains($msg, 'Nota: Sin crema'), 'la nota del cliente llega al mensaje');

$domicilio = array_merge($limpio, ['entrega' => 'domicilio', 'direccion' => 'Calle 33 #74-20', 'barrio' => 'Laureles']);
$msg2 = pedido_mensaje(ajuste('mensaje_pedido'), $domicilio, $items);
afirmar(str_contains($msg2, 'Domicilio: $5.000') && str_contains($msg2, 'Total a pagar: $24.000'), 'domicilio de $5.000 suma al total');

// 10. Enlace de WhatsApp: solo dígitos y mensaje codificado
$enlace = whatsapp_url('+57 324 524 5615', "Hola\n¡Gracias!");
afirmar(str_starts_with($enlace, 'https://wa.me/573245245615?text='), 'el número de WhatsApp queda solo con dígitos');
afirmar(!str_contains($enlace, "\n"), 'los saltos de línea van codificados en la URL');

// 11. Mesas: una por línea, sin duplicados ni líneas vacías
$n = mesas_reemplazar("Mesa 1\n\nMesa 2\nMesa 1\n  Barra  ");
afirmar($n === 3, 'mesas: quita duplicados y líneas vacías');
afirmar(array_column(mesas_listar(), 'nombre') === ['Mesa 1', 'Mesa 2', 'Barra'], 'mesas: conserva el orden escrito');
mesas_generar(12);
afirmar((int) db()->query('SELECT COUNT(*) FROM mesas')->fetchColumn() === 13, 'generar 12 agrega solo las que faltan (3 existentes + 10 nuevas)');
esperar_error(fn () => mesas_generar(0), 'no genera cero mesas');

// 12. Categorías: no borrar una que tiene productos
esperar_error(fn () => categoria_eliminar($idCono), 'no borra una categoría con productos');
esperar_error(fn () => categoria_crear('Conos'), 'no crea categorías repetidas');

// 13. Ruta de QR: index.php con el parámetro de mesa
$_SERVER['HTTP_HOST'] = 'localhost:8000';
$_SERVER['SCRIPT_NAME'] = '/index.php';
afirmar(str_ends_with(url_absoluta(['mesa' => 'Mesa 5']), 'localhost:8000/index.php?mesa=Mesa+5'), 'QR apunta a index.php con la mesa');

// 14. Filtro del menú por texto y categoría
$lista = [
    ['nombre' => 'Cono Arequipe', 'descripcion' => 'Con chips', 'categoria' => 'Conos'],
    ['nombre' => 'Canasta Pistacho', 'descripcion' => '', 'categoria' => 'Canastas'],
];
afirmar(count(productos_filtrar($lista, 'AREQUIPE', '')) === 1, 'buscador ignora mayúsculas y encuentra por nombre');
afirmar(count(productos_filtrar($lista, 'chips', '')) === 1, 'buscador también mira la descripción');
afirmar(count(productos_filtrar($lista, '', 'Canastas')) === 1, 'filtro por categoría');
afirmar(productos_filtrar($lista, 'fresa', '') === [], 'búsqueda sin resultados devuelve lista vacía');

// 15. Pedidos guardados, estados y CSV
$_SESSION['carrito'] = [$arequipe => 2];
$_SESSION['mesa'] = 'Mesa 5';
$itemsPedido = carrito_items();
$datosPedido = array_merge($limpio, ['entrega' => 'mesa']);
$idPedido = pedido_guardar($datosPedido, $itemsPedido);
$guardado = pedido_obtener($idPedido);
afirmar($guardado !== null && (int) $guardado['total'] === 19000, 'el pedido se guarda con su total');
afirmar($guardado['mesa'] === 'Mesa 5' && $guardado['estado'] === 'pendiente', 'el pedido guarda la mesa y arranca pendiente');
afirmar($guardado['lineas'][0]['nombre'] === 'Cono Arequipe' && $guardado['lineas'][0]['cantidad'] === 2, 'el pedido guarda las líneas con cantidad');
pedido_cambiar_estado($idPedido, 'entregado');
afirmar(pedido_obtener($idPedido)['estado'] === 'entregado', 'cambiar estado funciona');
esperar_error(fn () => pedido_cambiar_estado($idPedido, 'volando'), 'no acepta un estado inventado');
afirmar(pedidos_resumen_hoy()['pedidos'] === 1, 'resumen de hoy cuenta el pedido entregado');
afirmar(csv_celda('=2+2') === '"\'=2+2"', 'CSV protege celdas que empiezan con =');
afirmar(csv_celda('Ana "la"') === '"Ana ""la"""', 'CSV duplica las comillas');
$csv = pedidos_csv();
afirmar(str_starts_with($csv, "\xEF\xBB\xBF") && str_contains($csv, '2x Cono Arequipe'), 'CSV tiene BOM y lista los productos');
unset($_SESSION['mesa']);

// 17. Página de contacto
ob_start();
ctrl_contacto();
$htmlContacto = ob_get_clean();
afirmar(str_contains($htmlContacto, 'Datos de contacto') && str_contains($htmlContacto, ajuste('telefono')), 'la página de contacto muestra el teléfono del negocio');
afirmar(str_contains($htmlContacto, 'name="mensaje"') && str_contains($htmlContacto, '_csrf'), 'el formulario de contacto lleva el mensaje y la clave CSRF');

// Limpieza
@unlink($temporal);

echo "\n\n";
if ($fallos === []) {
    echo "OK: $ok pruebas pasaron.\n";
    exit(0);
}
echo "FALLARON " . count($fallos) . " de " . ($ok + count($fallos)) . ":\n";
foreach ($fallos as $f) {
    echo " - $f\n";
}
exit(1);
