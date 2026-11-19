<?php
declare(strict_types=1);

const ENTREGAS = [
    'domicilio' => 'Domicilio',
    'recoger' => 'Recoger en tienda',
    'mesa' => 'En mi mesa',
];

const PAGOS = ['Efectivo', 'Nequi', 'Transferencia'];

/**
 * Valida el formulario "Datos para tu pedido".
 * Devuelve [errores por campo, datos limpios].
 */
function pedido_validar(array $entrada): array
{
    $texto = static fn (string $clave): string => trim((string) ($entrada[$clave] ?? ''));

    $datos = [
        'nombre' => mb_substr($texto('nombre'), 0, 80),
        'telefono' => mb_substr($texto('telefono'), 0, 25),
        'entrega' => $texto('entrega'),
        'direccion' => mb_substr($texto('direccion'), 0, 120),
        'barrio' => mb_substr($texto('barrio'), 0, 60),
        'pago' => $texto('pago'),
        'nota' => mb_substr($texto('nota'), 0, 300),
    ];

    $errores = [];
    if (mb_strlen($datos['nombre']) < 2) {
        $errores['nombre'] = 'Escribe tu nombre.';
    }
    if (strlen(preg_replace('/\D+/', '', $datos['telefono'])) < 7) {
        $errores['telefono'] = 'Escribe un teléfono válido.';
    }
    if (!array_key_exists($datos['entrega'], ENTREGAS)) {
        $errores['entrega'] = 'Elige cómo quieres recibirlo.';
    } elseif ($datos['entrega'] === 'mesa' && mesa_actual() === null) {
        $errores['entrega'] = 'Para "En mi mesa" primero escanea el código QR de tu mesa.';
    }
    if (!in_array($datos['pago'], PAGOS, true)) {
        $errores['pago'] = 'Elige un método de pago.';
    }
    if ($datos['entrega'] === 'domicilio') {
        if (mb_strlen($datos['direccion']) < 5) {
            $errores['direccion'] = 'Escribe la dirección.';
        }
        if (mb_strlen($datos['barrio']) < 2) {
            $errores['barrio'] = 'Escribe el barrio.';
        }
    }

    return [$errores, $datos];
}

/** Costo del domicilio: solo aplica si el pedido es a domicilio. */
function pedido_domicilio(string $entrega): int
{
    return $entrega === 'domicilio' ? (int) ajuste('domicilio_costo') : 0;
}

/** Líneas del pedido en texto, una por producto. */
function pedido_lineas(array $items): string
{
    $lineas = [];
    foreach ($items as $item) {
        $lineas[] = '• ' . $item['cantidad'] . ' x ' . $item['producto']['nombre'] . ' — ' . dinero($item['linea']);
    }
    return implode("\n", $lineas);
}

/**
 * Reemplaza las {llaves} de la plantilla del negocio con los datos del pedido.
 * Es la misma función que usa la vista previa del panel.
 */
function pedido_mensaje(string $plantilla, array $datos, array $items): string
{
    $subtotal = carrito_totales($items)['subtotal'];
    $domicilio = pedido_domicilio($datos['entrega']);
    $esDomicilio = $datos['entrega'] === 'domicilio';

    $entrega = ENTREGAS[$datos['entrega']] ?? $datos['entrega'];
    if ($datos['entrega'] === 'mesa' && mesa_actual() !== null) {
        $entrega .= ' (' . mesa_actual() . ')';
    }

    $reemplazos = [
        '{negocio}' => ajuste('negocio_nombre'),
        '{pedido}' => pedido_lineas($items),
        '{subtotal}' => dinero($subtotal),
        '{domicilio}' => $esDomicilio ? dinero($domicilio) : 'No aplica',
        '{total}' => dinero($subtotal + $domicilio),
        '{nombre}' => $datos['nombre'],
        '{telefono}' => $datos['telefono'],
        '{entrega}' => $entrega,
        '{direccion}' => $esDomicilio ? $datos['direccion'] . ', ' . $datos['barrio'] : 'No aplica',
        '{pago}' => $datos['pago'],
        '{nota}' => $datos['nota'] !== '' ? $datos['nota'] : 'Ninguna',
    ];

    return strtr($plantilla, $reemplazos);
}

/** Enlace de WhatsApp con el mensaje ya escrito. */
function whatsapp_url(string $numero, string $mensaje): string
{
    $solo = preg_replace('/\D+/', '', $numero);
    return 'https://wa.me/' . $solo . '?text=' . rawurlencode($mensaje);
}

/* ---------- Pedidos guardados ---------- */

const ESTADOS_PEDIDO = [
    'pendiente' => 'Pendiente',
    'confirmado' => 'Confirmado',
    'entregado' => 'Entregado',
    'cancelado' => 'Cancelado',
];

/** Subtotal, domicilio y total de un pedido. Misma regla que el mensaje de WhatsApp. */
function pedido_totales(array $datos, array $items): array
{
    $subtotal = carrito_totales($items)['subtotal'];
    $domicilio = pedido_domicilio($datos['entrega']);
    return ['subtotal' => $subtotal, 'domicilio' => $domicilio, 'total' => $subtotal + $domicilio];
}

/** Guarda el pedido en la base para verlo en el panel. Devuelve su número. */
function pedido_guardar(array $datos, array $items): int
{
    $totales = pedido_totales($datos, $items);
    $lineas = array_map(static fn (array $i): array => [
        'nombre' => $i['producto']['nombre'],
        'cantidad' => $i['cantidad'],
        'precio' => (int) $i['producto']['precio'],
        'linea' => $i['linea'],
    ], $items);

    $st = db()->prepare(
        'INSERT INTO pedidos (creado_en, nombre, telefono, entrega, mesa, direccion, barrio, pago, nota,
                              items, subtotal, domicilio, total, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $st->execute([
        date('Y-m-d H:i:s'),
        $datos['nombre'],
        $datos['telefono'],
        ENTREGAS[$datos['entrega']] ?? $datos['entrega'],
        $datos['entrega'] === 'mesa' ? (string) mesa_actual() : '',
        $datos['entrega'] === 'domicilio' ? $datos['direccion'] : '',
        $datos['entrega'] === 'domicilio' ? $datos['barrio'] : '',
        $datos['pago'],
        $datos['nota'],
        json_encode($lineas, JSON_UNESCAPED_UNICODE),
        $totales['subtotal'],
        $totales['domicilio'],
        $totales['total'],
        'pendiente',
    ]);
    return (int) db()->lastInsertId();
}

/** Pedidos más recientes, opcionalmente filtrados por estado. */
function pedidos_listar(string $estado = ''): array
{
    if ($estado !== '' && array_key_exists($estado, ESTADOS_PEDIDO)) {
        $st = db()->prepare('SELECT * FROM pedidos WHERE estado = ? ORDER BY id DESC LIMIT 300');
        $st->execute([$estado]);
        return $st->fetchAll();
    }
    return db()->query('SELECT * FROM pedidos ORDER BY id DESC LIMIT 300')->fetchAll();
}

function pedido_obtener(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM pedidos WHERE id = ?');
    $st->execute([$id]);
    $fila = $st->fetch();
    if (!$fila) {
        return null;
    }
    $fila['lineas'] = json_decode((string) $fila['items'], true) ?: [];
    return $fila;
}

function pedido_cambiar_estado(int $id, string $estado): void
{
    if (!array_key_exists($estado, ESTADOS_PEDIDO)) {
        throw new InvalidArgumentException('Estado no válido.');
    }
    db()->prepare('UPDATE pedidos SET estado = ? WHERE id = ?')->execute([$estado, $id]);
}

/** Cifras del día: pedidos, total vendido y pendientes. */
function pedidos_resumen_hoy(): array
{
    $hoy = date('Y-m-d') . '%';
    $st = db()->prepare(
        "SELECT COUNT(*) AS pedidos, COALESCE(SUM(total), 0) AS total
         FROM pedidos WHERE creado_en LIKE ? AND estado != 'cancelado'"
    );
    $st->execute([$hoy]);
    $hoyFila = $st->fetch();
    $pendientes = (int) db()->query("SELECT COUNT(*) FROM pedidos WHERE estado = 'pendiente'")->fetchColumn();
    return [
        'pedidos' => (int) $hoyFila['pedidos'],
        'total' => (int) $hoyFila['total'],
        'pendientes' => $pendientes,
    ];
}

/** Protege el CSV contra fórmulas: si una celda empieza con = + - @ se le antepone una comilla. */
function csv_celda(mixed $valor): string
{
    $texto = (string) $valor;
    if ($texto !== '' && str_contains('=+-@', $texto[0])) {
        $texto = "'" . $texto;
    }
    return '"' . str_replace('"', '""', $texto) . '"';
}

/** Todos los pedidos como CSV (separado por punto y coma, para Excel en Colombia). */
function pedidos_csv(): string
{
    $cabecera = ['Número', 'Fecha', 'Cliente', 'Teléfono', 'Entrega', 'Mesa', 'Dirección', 'Barrio', 'Pago', 'Nota', 'Productos', 'Subtotal', 'Domicilio', 'Total', 'Estado'];
    $salida = "\xEF\xBB\xBF" . implode(';', array_map('csv_celda', $cabecera)) . "\r\n";
    foreach (db()->query('SELECT * FROM pedidos ORDER BY id')->fetchAll() as $p) {
        $productos = array_map(
            static fn (array $l): string => $l['cantidad'] . 'x ' . $l['nombre'],
            json_decode((string) $p['items'], true) ?: []
        );
        $fila = [
            $p['id'], $p['creado_en'], $p['nombre'], $p['telefono'], $p['entrega'], $p['mesa'],
            $p['direccion'], $p['barrio'], $p['pago'], $p['nota'], implode(', ', $productos),
            $p['subtotal'], $p['domicilio'], $p['total'], ESTADOS_PEDIDO[$p['estado']] ?? $p['estado'],
        ];
        $salida .= implode(';', array_map('csv_celda', $fila)) . "\r\n";
    }
    return $salida;
}
