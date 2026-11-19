<?php
declare(strict_types=1);

/**
 * Arranque común: configuración, sesión y carga de los módulos de src/.
 * Lo incluyen public/index.php, bin/setup.php y tests/run.php.
 */

function config(): array
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require __DIR__ . '/../config/config.php';
        // Permite a las pruebas usar una base de datos temporal.
        $ruta = getenv('LUPA_DB_PATH');
        if ($ruta !== false && $ruta !== '') {
            $cfg['db_path'] = $ruta;
        }
    }
    return $cfg;
}

date_default_timezone_set(config()['zona_horaria']);

if (PHP_SAPI !== 'cli') {
    session_name(config()['sesion_nombre']);
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/']);
    session_start();
}

require_once __DIR__ . '/util.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/tienda.php';
require_once __DIR__ . '/carrito.php';
require_once __DIR__ . '/pedidos.php';
require_once __DIR__ . '/ctrl_publico.php';
require_once __DIR__ . '/ctrl_admin.php';
require_once __DIR__ . '/rutas.php';
