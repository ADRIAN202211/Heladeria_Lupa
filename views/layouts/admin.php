<?php
/** Layout del panel del administrador. Variables: $titulo, $contenido, $seccion. */
$aviso = flash();
$logueado = es_admin();
$secciones = [
    'pedidos' => ['Pedidos', 'admin/pedidos'],
    'productos' => ['Productos', 'admin/productos'],
    'categorias' => ['Categorías', 'admin/categorias'],
    'mesas' => ['Mesas', 'admin/mesas'],
    'whatsapp' => ['WhatsApp y mensaje', 'admin/whatsapp'],
    'negocio' => ['Negocio y promoción', 'admin/negocio'],
];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="panel">

<header class="panel-barra">
    <a class="panel-marca" href="<?= e(url('admin/productos')) ?>">
        <?php require __DIR__ . '/../parciales/logo.php'; ?>
    </a>
    <div class="panel-botones">
        <?php if ($logueado): ?>
            <a class="btn btn-blanco" href="<?= e(url('menu')) ?>" target="_blank" rel="noopener">Ver menú ↗</a>
            <form method="post" action="<?= e(url('admin/salir')) ?>">
                <?= csrf_campo() ?>
                <button class="btn btn-borde" type="submit">Salir</button>
            </form>
        <?php endif; ?>
    </div>
</header>

<?php if ($logueado): ?>
<nav class="panel-tabs" aria-label="Secciones del panel">
    <?php foreach ($secciones as $clave => [$etiqueta, $ruta]): ?>
        <a href="<?= e(url($ruta)) ?>" class="<?= ($seccion ?? '') === $clave ? 'activo' : '' ?>"><?= e($etiqueta) ?></a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>

<?php if ($aviso): ?>
    <div class="aviso aviso-<?= e($aviso['tipo']) ?> aviso-panel" role="status"><?= e($aviso['mensaje']) ?></div>
<?php endif; ?>

<main class="panel-contenido">
    <?= $contenido ?>
</main>

<script src="assets/app.js"></script>
</body>
</html>
