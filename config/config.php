<?php
/**
 * Configuración por defecto de Heladería Lupa.
 *
 * Los datos del negocio (dirección, horario, WhatsApp, costo de domicilio,
 * mensaje del pedido) se cambian desde el panel de administración; estos
 * valores son solo los iniciales que usa bin/setup.php.
 */
return [
    'db_path'          => __DIR__ . '/../data/lupa.sqlite',
    'uploads_dir'      => __DIR__ . '/../public/uploads',
    'max_upload_bytes' => 2 * 1024 * 1024,
    'zona_horaria'     => 'America/Bogota',
    'sesion_nombre'    => 'lupa_sess',
    'cantidad_maxima'  => 99,

    // Datos del negocio iniciales. Se cambian desde el panel (Negocio y promoción).
    'ajustes' => [
        'negocio_nombre'  => 'Heladería Lupa',
        'direccion'       => 'Carrera 76 #39B-45, Laureles, Medellín, Antioquia',
        'horario'         => 'Lunes a domingo · 11:00 a. m. – 10:00 p. m.',
        'telefono'        => '+57 324 524 5615',
        'correo'          => 'hola@heladerialupa.co',
        'whatsapp'        => '573245245615',
        'domicilio_costo' => '5000',
        'sobre_nosotros'  => '',
        'mensaje_pedido'  => "¡Hola {negocio}! 🍦 Quiero hacer este pedido:\n\n"
            . "{pedido}\n\n"
            . "Subtotal: {subtotal}\n"
            . "Domicilio: {domicilio}\n"
            . "*Total a pagar: {total}*\n\n"
            . "👤 Nombre: {nombre}\n"
            . "📱 Teléfono: {telefono}\n"
            . "🛵 Entrega: {entrega}\n"
            . "📍 Dirección: {direccion}\n"
            . "💳 Pago: {pago}\n"
            . "📝 Nota: {nota}\n\n"
            . "¡Gracias!",
    ],

    // Menú de ejemplo con los datos del manual. Fotos: recortes de las capturas del sitio; las demás son ilustraciones.
    // Columnas: categoría, nombre, descripción, precio, etiqueta, imagen
    'productos_iniciales' => [
        ['Conos', 'Cono Clásico de Vainilla', 'Helado de vainilla con crema chantilly y cereza en almíbar.', 8000, '', 'assets/productos/foto-cono-vainilla.png'],
        ['Conos', 'Cono Arequipe con Chips', 'Helado de arequipe casero con chips de chocolate negro.', 9500, 'Favorito', 'assets/productos/foto-cono-arequipe.png'],
        ['Conos', 'Cono Fresa Silvestre', 'Helado de fresas frescas del oriente antioqueño con trozos de chocolate.', 9500, '', 'assets/productos/foto-cono-fresa.png'],
        ['Conos', 'Cono Red Velvet', 'Helado de frutos rojos sobre brownie red velvet y chispas de colores.', 10500, 'Nuevo', 'assets/productos/foto-cono-red-velvet.png'],
        ['Canastas', 'Canasta de Mora Andina', 'Tres bolas de helado de mora andina en barquillo.', 10000, '', 'assets/productos/canasta-mora.svg'],
        ['Canastas', 'Canasta Chocolate Belga', 'Tres bolas de helado de chocolate belga.', 10500, '', 'assets/productos/foto-bowl-chocolate.png'],
        ['Canastas', 'Canasta de Pistacho', 'Tres bolas de helado de pistacho.', 11000, '', 'assets/productos/canasta-pistacho.svg'],
        ['Copas', 'Copa Frutal Lupa', 'Copa de helado con frutas de temporada y salsa de la casa.', 17500, '', 'assets/productos/copa-frutal.svg'],
    ],

    'mesas_iniciales' => ['Mesa 1', 'Mesa 2', 'Mesa 3', 'Mesa 4', 'Mesa 5', 'Mesa 6', 'Mesa 7', 'Mesa 8', 'Mesa 9', 'Mesa 10', 'Terraza 1', 'Terraza 2'],
];
