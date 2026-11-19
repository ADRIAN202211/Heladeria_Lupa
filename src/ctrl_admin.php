<?php
declare(strict_types=1);

/* Panel del administrador: productos, categorías, mesas, WhatsApp y negocio. */

function admin_datos(string $seccion, array $extra = []): array
{
    return array_merge(['seccion' => $seccion], $extra);
}

function ctrl_admin_entrar(): void
{
    if (es_admin()) {
        redirigir(url('admin/productos'));
    }
    render('admin/login', ['seccion' => 'login'], 'admin');
}

function ctrl_admin_login(): void
{
    csrf_verificar();
    $hash = ajuste('admin_hash');
    $clave = (string) ($_POST['clave'] ?? '');

    if ($hash === '' || !password_verify($clave, $hash)) {
        flash('Contraseña incorrecta.', 'error');
        redirigir(url('admin'));
    }
    session_regenerate_id(true);
    $_SESSION['admin'] = true;
    redirigir(url('admin/productos'));
}

function ctrl_admin_salir(): void
{
    csrf_verificar();
    unset($_SESSION['admin']);
    flash('Sesión cerrada.');
    redirigir(url('admin'));
}

/* ---------- Productos ---------- */

function ctrl_admin_productos(): void
{
    requerir_admin();
    render('admin/productos', admin_datos('productos', [
        'productos' => productos_listar(),
        'titulo' => 'Productos · Panel Lupa',
    ]), 'admin');
}

function ctrl_admin_producto_form(): void
{
    requerir_admin();
    $id = (int) ($_GET['id'] ?? 0);
    $producto = $id > 0 ? producto_obtener($id) : null;
    if ($id > 0 && $producto === null) {
        flash('Ese producto ya no existe.', 'error');
        redirigir(url('admin/productos'));
    }

    // Si el guardado falló, se muestran los datos que la persona ya había escrito.
    $viejos = $_SESSION['producto_viejo'] ?? null;
    unset($_SESSION['producto_viejo']);
    if (is_array($viejos)) {
        $producto = array_merge($producto ?? [], $viejos);
    }

    render('admin/producto_form', admin_datos('productos', [
        'producto' => $producto ?? [],
        'categorias' => categorias_listar(),
        'titulo' => ($producto ? 'Editar producto' : 'Nuevo producto') . ' · Panel Lupa',
    ]), 'admin');
}

function ctrl_admin_producto_guardar(): void
{
    requerir_admin();
    csrf_verificar();
    $id = (int) ($_POST['id'] ?? 0) ?: null;
    $ruta = $id ? url('admin/producto', ['id' => $id]) : url('admin/producto');

    $imagen = null;
    try {
        $imagen = imagen_guardar($_FILES['imagen'] ?? []);
        producto_guardar($_POST, $id, $imagen);
        flash('Producto guardado. El menú del cliente ya se actualizó.');
        redirigir(url('admin/productos'));
    } catch (InvalidArgumentException | RuntimeException $e) {
        if ($imagen !== null) {
            imagen_borrar($imagen); // no dejar archivos huérfanos si el guardado falló
        }
        $_SESSION['producto_viejo'] = array_diff_key($_POST, ['_csrf' => true, 'id' => true]);
        flash($e->getMessage(), 'error');
        redirigir($ruta);
    }
}

function ctrl_admin_producto_mover(): void
{
    requerir_admin();
    csrf_verificar();
    producto_mover((int) ($_POST['id'] ?? 0), ($_POST['direccion'] ?? '') === 'subir' ? 'subir' : 'bajar');
    redirigir(url('admin/productos'));
}

function ctrl_admin_producto_disponible(): void
{
    requerir_admin();
    csrf_verificar();
    producto_alternar_disponible((int) ($_POST['id'] ?? 0));
    redirigir(url('admin/productos'));
}

function ctrl_admin_producto_eliminar(): void
{
    requerir_admin();
    csrf_verificar();
    producto_eliminar((int) ($_POST['id'] ?? 0));
    flash('Producto eliminado.');
    redirigir(url('admin/productos'));
}

/* ---------- Categorías ---------- */

function ctrl_admin_categorias(): void
{
    requerir_admin();
    render('admin/categorias', admin_datos('categorias', [
        'categorias' => categorias_listar(),
        'titulo' => 'Categorías · Panel Lupa',
    ]), 'admin');
}

function ctrl_admin_categoria_guardar(): void
{
    requerir_admin();
    csrf_verificar();
    try {
        categoria_crear((string) ($_POST['nombre'] ?? ''));
        flash('Categoría creada.');
    } catch (InvalidArgumentException $e) {
        flash($e->getMessage(), 'error');
    }
    redirigir(url('admin/categorias'));
}

function ctrl_admin_categoria_eliminar(): void
{
    requerir_admin();
    csrf_verificar();
    try {
        categoria_eliminar((int) ($_POST['id'] ?? 0));
        flash('Categoría eliminada.');
    } catch (InvalidArgumentException $e) {
        flash($e->getMessage(), 'error');
    }
    redirigir(url('admin/categorias'));
}

/* ---------- Mesas y QR ---------- */

function ctrl_admin_mesas(): void
{
    requerir_admin();
    $mesas = mesas_listar();
    foreach ($mesas as &$mesa) {
        $mesa['enlace'] = url_absoluta(['mesa' => $mesa['nombre']]);
    }
    unset($mesa);

    render('admin/mesas', admin_datos('mesas', [
        'mesas' => $mesas,
        'titulo' => 'Mesas y QR · Panel Lupa',
    ]), 'admin');
}

function ctrl_admin_mesas_guardar(): void
{
    requerir_admin();
    csrf_verificar();
    try {
        $n = mesas_reemplazar((string) ($_POST['mesas'] ?? ''));
        flash("Mesas guardadas: $n. Imprime los QR nuevos.");
    } catch (InvalidArgumentException $e) {
        flash($e->getMessage(), 'error');
    }
    redirigir(url('admin/mesas'));
}

function ctrl_admin_mesas_generar(): void
{
    requerir_admin();
    csrf_verificar();
    try {
        $n = mesas_generar((int) ($_POST['cantidad'] ?? 0));
        flash("Listo: Mesa 1 a Mesa $n.");
    } catch (InvalidArgumentException $e) {
        flash($e->getMessage(), 'error');
    }
    redirigir(url('admin/mesas'));
}

/* ---------- WhatsApp y mensaje ---------- */

function ctrl_admin_whatsapp(): void
{
    requerir_admin();
    render('admin/whatsapp', admin_datos('whatsapp', [
        'titulo' => 'WhatsApp y mensaje · Panel Lupa',
        'previa' => vista_mensaje(ajuste('mensaje_pedido')),
    ]), 'admin');
}

function ctrl_admin_whatsapp_guardar(): void
{
    requerir_admin();
    csrf_verificar();
    $numero = preg_replace('/\D+/', '', (string) ($_POST['whatsapp'] ?? ''));
    $costo = preg_replace('/\D+/', '', (string) ($_POST['domicilio_costo'] ?? ''));
    $mensaje = trim((string) ($_POST['mensaje_pedido'] ?? ''));

    if (strlen($numero) < 10) {
        flash('El número de WhatsApp debe incluir el código de país. Ej: 573245245615', 'error');
        redirigir(url('admin/whatsapp'));
    }
    if ($costo === '') {
        $costo = '0';
    }
    if ($mensaje === '') {
        flash('El mensaje del pedido no puede quedar vacío.', 'error');
        redirigir(url('admin/whatsapp'));
    }

    ajustes_guardar([
        'whatsapp' => $numero,
        'domicilio_costo' => $costo,
        'mensaje_pedido' => $mensaje,
    ]);
    flash('Configuración de WhatsApp guardada.');
    redirigir(url('admin/whatsapp'));
}

/** Vista previa con un pedido de ejemplo, usando la misma función que el envío real. */
function vista_mensaje(string $plantilla): string
{
    $items = [
        ['producto' => ['nombre' => 'Cono Arequipe con Chips'], 'cantidad' => 2, 'linea' => 19000],
        ['producto' => ['nombre' => 'Copa Frutal Lupa'], 'cantidad' => 1, 'linea' => 17500],
    ];
    $datos = [
        'nombre' => 'Laura Restrepo',
        'telefono' => '300 123 4567',
        'entrega' => 'domicilio',
        'direccion' => 'Calle 33 #74-20, apto 301',
        'barrio' => 'Laureles',
        'pago' => 'Nequi',
        'nota' => '',
    ];
    return pedido_mensaje($plantilla, $datos, $items);
}

/* ---------- Negocio y promoción ---------- */

function ctrl_admin_negocio(): void
{
    requerir_admin();
    render('admin/negocio', admin_datos('negocio', [
        'titulo' => 'Negocio y promoción · Panel Lupa',
    ]), 'admin');
}

function ctrl_admin_negocio_guardar(): void
{
    requerir_admin();
    csrf_verificar();
    $correo = trim((string) ($_POST['correo'] ?? ''));
    $nombre = trim((string) ($_POST['negocio_nombre'] ?? ''));

    if ($nombre === '') {
        flash('El nombre del negocio no puede quedar vacío.', 'error');
        redirigir(url('admin/negocio'));
    }
    if ($correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
        flash('El correo no es válido.', 'error');
        redirigir(url('admin/negocio'));
    }

    ajustes_guardar([
        'sobre_nosotros' => mb_substr(trim((string) ($_POST['sobre_nosotros'] ?? '')), 0, 1500),
        'negocio_nombre' => mb_substr($nombre, 0, 80),
        'direccion' => mb_substr(trim((string) ($_POST['direccion'] ?? '')), 0, 160),
        'horario' => mb_substr(trim((string) ($_POST['horario'] ?? '')), 0, 120),
        'telefono' => mb_substr(trim((string) ($_POST['telefono'] ?? '')), 0, 40),
        'correo' => $correo,
    ]);
    flash('Datos del negocio guardados.');
    redirigir(url('admin/negocio'));
}

/* ---------- Pedidos ---------- */

function ctrl_admin_pedidos(): void
{
    requerir_admin();
    $estado = (string) ($_GET['estado'] ?? '');
    render('admin/pedidos', admin_datos('pedidos', [
        'pedidos' => pedidos_listar($estado),
        'resumen' => pedidos_resumen_hoy(),
        'estado' => $estado,
        'titulo' => 'Pedidos · Panel Lupa',
    ]), 'admin');
}

function ctrl_admin_pedido(): void
{
    requerir_admin();
    $pedido = pedido_obtener((int) ($_GET['id'] ?? 0));
    if ($pedido === null) {
        flash('Ese pedido no existe.', 'error');
        redirigir(url('admin/pedidos'));
    }
    render('admin/pedido', admin_datos('pedidos', [
        'pedido' => $pedido,
        'titulo' => 'Pedido #' . $pedido['id'] . ' · Panel Lupa',
    ]), 'admin');
}

function ctrl_admin_pedido_estado(): void
{
    requerir_admin();
    csrf_verificar();
    $id = (int) ($_POST['id'] ?? 0);
    try {
        pedido_cambiar_estado($id, (string) ($_POST['estado'] ?? ''));
        flash('Estado actualizado.');
    } catch (InvalidArgumentException $e) {
        flash($e->getMessage(), 'error');
    }
    redirigir(url('admin/pedido', ['id' => $id]));
}

function ctrl_admin_pedidos_csv(): void
{
    requerir_admin();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="pedidos-' . date('Y-m-d') . '.csv"');
    echo pedidos_csv();
    exit;
}
