<?php
declare(strict_types=1);

/** Escapa texto para HTML. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Formatea pesos colombianos: 8000 -> $8.000 */
function dinero(int $pesos): string
{
    return '$' . number_format($pesos, 0, ',', '.');
}

/** URL relativa a la página principal: url('menu') -> index.php?r=menu */
function url(string $ruta = 'inicio', array $extra = []): string
{
    // Se deja la barra "/" sin codificar para que se lea index.php?r=admin/productos
    $query = http_build_query(array_merge(['r' => $ruta], $extra));
    return 'index.php?' . str_replace('%2F', '/', $query);
}

/** URL absoluta de index.php con parámetros; se usa en los códigos QR. */
function url_absoluta(array $params): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    return ($https ? 'https' : 'http') . '://' . $host . $script . '?' . http_build_query($params);
}

function redirigir(string $destino): never
{
    header('Location: ' . $destino);
    exit;
}

/** Guarda un mensaje para mostrarlo en la siguiente página, o lo lee si no se pasa texto. */
function flash(?string $mensaje = null, string $tipo = 'ok'): ?array
{
    if ($mensaje !== null) {
        $_SESSION['flash'] = ['mensaje' => $mensaje, 'tipo' => $tipo];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/** Corta la petición si el formulario no trae el token de la sesión. */
function csrf_verificar(): void
{
    $esperado = $_SESSION['csrf'] ?? '';
    $recibido = $_POST['_csrf'] ?? '';
    if ($esperado === '' || !is_string($recibido) || !hash_equals($esperado, $recibido)) {
        http_response_code(419);
        exit('La sesión venció. Recarga la página e inténtalo de nuevo.');
    }
}

/** Renderiza una vista dentro de un layout. Las variables llegan por $datos. */
function render(string $__vista, array $__datos = [], string $__layout = 'publico'): void
{
    // Los nombres con __ evitan que una clave de $__datos (p. ej. "datos") choque con las variables de aquí.
    extract($__datos, EXTR_SKIP);
    $titulo = $titulo ?? config()['ajustes']['negocio_nombre'];
    ob_start();
    require __DIR__ . '/../views/' . $__vista . '.php';
    $contenido = ob_get_clean();
    require __DIR__ . '/../views/layouts/' . $__layout . '.php';
}

function es_admin(): bool
{
    return !empty($_SESSION['admin']);
}

/** Obliga a estar logueado en el panel; si no, manda al inicio de sesión. */
function requerir_admin(): void
{
    if (!es_admin()) {
        flash('Inicia sesión para entrar al panel.', 'error');
        redirigir(url('admin'));
    }
}
