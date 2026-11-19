<?php
/** Layout de las páginas del cliente. Variables: $titulo, $contenido, $seccion (opcional). */
$aviso = flash();
$nombreNegocio = ajuste('negocio_nombre');
$whatsapp = preg_replace('/\D+/', '', ajuste('whatsapp'));
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="publico">

<header class="cabecera">
    <a class="marca" href="<?= e(url('inicio')) ?>" aria-label="<?= e($nombreNegocio) ?>">
        <?php require __DIR__ . '/../parciales/logo.php'; ?>
    </a>

    <nav class="menu-principal" aria-label="Navegación">
        <a href="<?= e(url('inicio')) ?>" class="<?= ($seccion ?? '') === 'inicio' ? 'activo' : '' ?>">Inicio</a>
        <a href="<?= e(url('menu')) ?>" class="<?= ($seccion ?? '') === 'menu' ? 'activo' : '' ?>">Menú</a>
        <a href="<?= e(url('info')) ?>" class="<?= ($seccion ?? '') === 'info' ? 'activo' : '' ?>">Información</a>
        <a href="<?= e(url('carrito')) ?>" class="<?= ($seccion ?? '') === 'carrito' ? 'activo' : '' ?>">Carrito</a>
    </nav>

    <div class="acciones">
        <a class="icono-carrito" href="<?= e(url('carrito')) ?>" aria-label="Carrito, <?= carrito_unidades() ?> productos">
            <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8L5 8Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>
            <span class="contador"><?= carrito_unidades() ?></span>
        </a>
        <a class="btn btn-rosa" href="<?= e(url('menu')) ?>">Pedir ahora <span aria-hidden="true">→</span></a>
    </div>
</header>

<?php if ($aviso): ?>
    <div class="aviso aviso-<?= e($aviso['tipo']) ?>" role="status"><?= e($aviso['mensaje']) ?></div>
<?php endif; ?>

<main class="contenido">
    <?= $contenido ?>
</main>

<footer class="pie">
    <div class="pie-grid">
        <div class="pie-marca">
            <?php require __DIR__ . '/../parciales/logo.php'; ?>
        </div>

        <div>
            <h3>Navegación</h3>
            <ul class="pie-lista">
                <li><a href="<?= e(url('inicio')) ?>">Inicio</a></li>
                <li><a href="<?= e(url('menu')) ?>">Menú</a></li>
                <li><a href="<?= e(url('info')) ?>">Información</a></li>
                <li><a href="<?= e(url('carrito')) ?>">Carrito</a></li>
            </ul>
        </div>

        <div>
            <h3>Dirección</h3>
            <p><?= e(ajuste('direccion')) ?></p>
            <h3>Horario</h3>
            <p><?= e(ajuste('horario')) ?></p>
        </div>

        <div>
            <h3>Contacto</h3>
            <p><?= e(ajuste('telefono')) ?></p>
            <?php if ($whatsapp !== ''): ?>
                <p><a class="enlace-claro" href="https://wa.me/<?= e($whatsapp) ?>">Escríbenos por WhatsApp</a></p>
            <?php endif; ?>
            <?php if (ajuste('correo') !== ''): ?>
                <p><a class="enlace-claro" href="mailto:<?= e(ajuste('correo')) ?>"><?= e(ajuste('correo')) ?></a></p>
            <?php endif; ?>
        </div>
    </div>
    <p class="pie-legal">© <?= date('Y') ?> <?= e($nombreNegocio) ?> · Medellín, Colombia. Todos los derechos reservados.</p>
</footer>

<?php if ($whatsapp !== ''): ?>
    <a class="wa-flotante" href="https://wa.me/<?= e($whatsapp) ?>" aria-label="Escribir por WhatsApp">WhatsApp</a>
<?php endif; ?>

<button class="subir" id="subir" type="button" aria-label="Volver arriba">↑</button>
<script src="assets/app.js"></script>
</body>
</html>
