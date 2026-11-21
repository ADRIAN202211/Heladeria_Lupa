<?php
declare(strict_types=1);

/* Páginas que ve el cliente: inicio, menú, carrito y datos del pedido. */

function ctrl_inicio(): void
{
    render('publico/inicio', [
        'destacados' => array_slice(productos_listar(true), 0, 4),
    ]);
}

function ctrl_menu(): void
{
    $texto = trim((string) ($_GET['q'] ?? ''));
    $categoria = trim((string) ($_GET['cat'] ?? ''));
    $disponibles = productos_listar(true);
    $filtrados = productos_filtrar($disponibles, $texto, $categoria);

    // Agrupa por categoría respetando el orden de la lista del panel.
    $grupos = [];
    foreach ($filtrados as $producto) {
        $grupos[$producto['categoria'] ?? 'Otros'][] = $producto;
    }

    $categorias = [];
    foreach ($disponibles as $producto) {
        $categorias[$producto['categoria'] ?? 'Otros'] = true;
    }

    render('publico/menu', [
        'grupos' => $grupos,
        'categorias' => array_keys($categorias),
        'texto' => $texto,
        'categoria' => $categoria,
        'total' => count($filtrados),
        'mesas' => mesas_listar(),
    ]);
}

/** El cliente elige en el menú si está en una mesa o pide para llevar/domicilio. */
function ctrl_mesa_elegir(): void
{
    csrf_verificar();
    $elegida = trim((string) ($_POST['mesa'] ?? ''));
    if ($elegida === '') {
        mesa_limpiar();
        flash('Pedido para llevar o a domicilio.');
    } else {
        $existe = in_array($elegida, array_column(mesas_listar(), 'nombre'), true);
        if (!$existe) {
            flash('Esa mesa no existe.', 'error');
        } else {
            mesa_fijar($elegida);
            flash('Estás en ' . $elegida . '. Tu pedido llegará a esa mesa.');
        }
    }
    redirigir(url('menu'));
}

function ctrl_info(): void
{
    render('publico/info', [
        'titulo' => 'Información · ' . ajuste('negocio_nombre'),
    ]);
}

function ctrl_contacto(): void
{
    render('publico/contacto', [
        'titulo' => 'Contacto · ' . ajuste('negocio_nombre'),
    ]);
}

/** Recibe el mensaje del formulario y abre WhatsApp con el texto listo. */
function ctrl_contacto_enviar(): void
{
    csrf_verificar();
    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $mensaje = trim((string) ($_POST['mensaje'] ?? ''));
    if (mb_strlen($nombre) < 2 || $mensaje === '') {
        flash('Escribe tu nombre y un mensaje.', 'error');
        redirigir(url('contacto'));
    }
    $texto = 'Hola, soy ' . mb_substr($nombre, 0, 80) . '. ' . mb_substr($mensaje, 0, 500);
    redirigir(whatsapp_url(ajuste('whatsapp'), $texto));
}

function ctrl_carrito(): void
{
    $items = carrito_items();
    render('publico/carrito', [
        'items' => $items,
        'totales' => carrito_totales($items),
    ]);
}

function ctrl_carrito_accion(): void
{
    csrf_verificar();
    $accion = (string) ($_POST['accion'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    match ($accion) {
        'agregar' => carrito_agregar($id),
        'cambiar' => carrito_cambiar_cantidad($id, (int) ($_POST['cantidad'] ?? 0)),
        'quitar' => carrito_quitar($id),
        'vaciar' => carrito_vaciar(),
        default => null,
    };

    // Solo se acepta volver a páginas conocidas, nunca a una URL que llegue en el formulario.
    $destino = match ($_POST['volver'] ?? '') {
        'menu' => url('menu'),
        'inicio' => url('inicio'),
        default => url('carrito'),
    };
    redirigir($destino);
}

function ctrl_pedido(): void
{
    $items = carrito_items();
    if ($items === []) {
        flash('Tu carrito está vacío. Agrega algo del menú primero.', 'error');
        redirigir(url('carrito'));
    }

    $errores = $_SESSION['pedido_errores'] ?? [];
    $datos = $_SESSION['pedido_datos'] ?? [];
    unset($_SESSION['pedido_errores'], $_SESSION['pedido_datos']);

    render('publico/pedido', [
        'items' => $items,
        'totales' => carrito_totales($items),
        'errores' => $errores,
        'datos' => $datos,
        'mesa' => mesa_actual(),
        'costoDomicilio' => (int) ajuste('domicilio_costo'),
    ]);
}

function ctrl_pedido_enviar(): void
{
    csrf_verificar();
    $items = carrito_items();
    if ($items === []) {
        redirigir(url('carrito'));
    }

    [$errores, $datos] = pedido_validar($_POST);
    if ($errores !== []) {
        $_SESSION['pedido_errores'] = $errores;
        $_SESSION['pedido_datos'] = $datos;
        flash('Revisa los campos marcados.', 'error');
        redirigir(url('pedido'));
    }

    $numero = ajuste('whatsapp');
    if (preg_replace('/\D+/', '', $numero) === '') {
        flash('El negocio aún no tiene un número de WhatsApp configurado.', 'error');
        redirigir(url('pedido'));
    }

    // El pedido queda guardado en el panel antes de abrir WhatsApp.
    pedido_guardar($datos, $items);
    $mensaje = pedido_mensaje(ajuste('mensaje_pedido'), $datos, $items);
    // El carrito se conserva: si WhatsApp no abre, el cliente puede volver a intentarlo.
    redirigir(whatsapp_url($numero, $mensaje));
}

function ctrl_no_encontrado(): void
{
    http_response_code(404);
    render('publico/404', ['titulo' => 'Página no encontrada']);
}
