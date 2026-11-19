<?php
declare(strict_types=1);

/*
 * El carrito vive en la sesión: $_SESSION['carrito'] = [id_producto => cantidad].
 * Los precios siempre se leen de la base de datos, así un cambio de precio
 * se refleja al cliente sin reimprimir nada.
 */

function carrito_agregar(int $id): void
{
    $producto = producto_obtener($id);
    if ($producto === null || !(int) $producto['disponible']) {
        flash('Ese producto no está disponible en este momento.', 'error');
        return;
    }
    $actual = (int) ($_SESSION['carrito'][$id] ?? 0);
    $_SESSION['carrito'][$id] = min($actual + 1, config()['cantidad_maxima']);
    flash('Agregado: ' . $producto['nombre']);
}

function carrito_cambiar_cantidad(int $id, int $cantidad): void
{
    if ($cantidad <= 0) {
        carrito_quitar($id);
        return;
    }
    if (!isset($_SESSION['carrito'][$id])) {
        return;
    }
    $_SESSION['carrito'][$id] = min($cantidad, config()['cantidad_maxima']);
}

function carrito_quitar(int $id): void
{
    unset($_SESSION['carrito'][$id]);
}

function carrito_vaciar(): void
{
    $_SESSION['carrito'] = [];
}

/**
 * Devuelve las líneas del carrito con datos del producto.
 * Quita de la sesión los productos que ya no existen o están agotados.
 */
function carrito_items(): array
{
    $ids = $_SESSION['carrito'] ?? [];
    if ($ids === []) {
        return [];
    }

    $marcas = implode(',', array_fill(0, count($ids), '?'));
    $st = db()->prepare(
        "SELECT p.*, c.nombre AS categoria FROM productos p
         LEFT JOIN categorias c ON c.id = p.categoria_id
         WHERE p.id IN ($marcas)"
    );
    $st->execute(array_map('intval', array_keys($ids)));

    $items = [];
    $vigentes = [];
    foreach ($st->fetchAll() as $p) {
        if (!(int) $p['disponible']) {
            continue;
        }
        $cantidad = (int) $ids[(int) $p['id']];
        $vigentes[(int) $p['id']] = true;
        $items[] = [
            'producto' => $p,
            'cantidad' => $cantidad,
            'linea' => $cantidad * (int) $p['precio'],
        ];
    }

    $quitados = 0;
    foreach (array_keys($ids) as $id) {
        if (!isset($vigentes[(int) $id])) {
            unset($_SESSION['carrito'][$id]);
            $quitados++;
        }
    }
    if ($quitados > 0) {
        flash('Algunos productos ya no están disponibles y se quitaron del carrito.', 'error');
    }

    return $items;
}

/** Subtotal y cantidad total de unidades. */
function carrito_totales(array $items): array
{
    $subtotal = 0;
    $unidades = 0;
    foreach ($items as $item) {
        $subtotal += $item['linea'];
        $unidades += $item['cantidad'];
    }
    return ['subtotal' => $subtotal, 'unidades' => $unidades];
}

/** Unidades para el icono del carrito (sin consultar la base). */
function carrito_unidades(): int
{
    return array_sum($_SESSION['carrito'] ?? []);
}

/** Mesa escaneada desde el código QR, o null. */
function mesa_actual(): ?string
{
    return $_SESSION['mesa'] ?? null;
}

function mesa_fijar(string $nombre): void
{
    $_SESSION['mesa'] = mb_substr(trim($nombre), 0, 40);
}

/** Vuelve a "Para llevar / domicilio". */
function mesa_limpiar(): void
{
    unset($_SESSION['mesa']);
}
