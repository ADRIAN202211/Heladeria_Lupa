<?php
declare(strict_types=1);

/**
 * Tabla de rutas. La clave es "MÉTODO ruta" y la ruta sale de ?r=...
 * Ejemplo: index.php?r=menu  ·  index.php?r=admin/productos
 */
function despachar(string $ruta, string $metodo): void
{
    $tabla = [
        'GET inicio' => 'ctrl_inicio',
        'GET menu' => 'ctrl_menu',
        'GET info' => 'ctrl_info',
        'POST mesa' => 'ctrl_mesa_elegir',
        'GET carrito' => 'ctrl_carrito',
        'POST carrito' => 'ctrl_carrito_accion',
        'GET pedido' => 'ctrl_pedido',
        'POST pedido' => 'ctrl_pedido_enviar',

        'GET admin' => 'ctrl_admin_entrar',
        'POST admin/login' => 'ctrl_admin_login',
        'POST admin/salir' => 'ctrl_admin_salir',

        'GET admin/pedidos' => 'ctrl_admin_pedidos',
        'GET admin/pedido' => 'ctrl_admin_pedido',
        'POST admin/pedido/estado' => 'ctrl_admin_pedido_estado',
        'GET admin/pedidos/csv' => 'ctrl_admin_pedidos_csv',

        'GET admin/productos' => 'ctrl_admin_productos',
        'GET admin/producto' => 'ctrl_admin_producto_form',
        'POST admin/producto' => 'ctrl_admin_producto_guardar',
        'POST admin/producto/mover' => 'ctrl_admin_producto_mover',
        'POST admin/producto/disponible' => 'ctrl_admin_producto_disponible',
        'POST admin/producto/eliminar' => 'ctrl_admin_producto_eliminar',

        'GET admin/categorias' => 'ctrl_admin_categorias',
        'POST admin/categorias' => 'ctrl_admin_categoria_guardar',
        'POST admin/categorias/eliminar' => 'ctrl_admin_categoria_eliminar',

        'GET admin/mesas' => 'ctrl_admin_mesas',
        'POST admin/mesas' => 'ctrl_admin_mesas_guardar',
        'POST admin/mesas/generar' => 'ctrl_admin_mesas_generar',

        'GET admin/whatsapp' => 'ctrl_admin_whatsapp',
        'POST admin/whatsapp' => 'ctrl_admin_whatsapp_guardar',

        'GET admin/negocio' => 'ctrl_admin_negocio',
        'POST admin/negocio' => 'ctrl_admin_negocio_guardar',
    ];

    $clave = $metodo . ' ' . $ruta;
    if (!isset($tabla[$clave])) {
        ctrl_no_encontrado();
        return;
    }
    $tabla[$clave]();
}
