<?php
declare(strict_types=1);

/*
 * Punto de entrada único. Todas las páginas van por index.php?r=...
 * Así funcionan los QR (index.php?mesa=...) sin reglas de reescritura del servidor.
 */
require __DIR__ . '/../src/bootstrap.php';

// Al escanear el QR de una mesa llega ?mesa=Mesa+5: se guarda en la sesión y se abre el menú.
if (isset($_GET['mesa']) && trim((string) $_GET['mesa']) !== '') {
    mesa_fijar((string) $_GET['mesa']);
    if (!isset($_GET['r'])) {
        redirigir(url('menu'));
    }
}

$ruta = (string) ($_GET['r'] ?? 'inicio');
$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    despachar($ruta, $metodo);
} catch (Throwable $e) {
    // En local se muestra el detalle para depurar; en producción conviene registrarlo en un log.
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>Error</title>'
        . '<main style="font-family:system-ui;padding:2rem"><h1>Algo salió mal</h1>'
        . '<p>Revisa la terminal donde corre el servidor.</p></main>';
    error_log('[lupa] ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
}
