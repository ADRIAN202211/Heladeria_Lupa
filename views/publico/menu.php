<?php $seccion = 'menu'; ?>
<section class="bloque bloque-menu">
    <h1 class="titulo-pagina">Nuestro menú</h1>
    <p class="subtitulo">Elige tus favoritos, agrégalos al carrito y envíanos el pedido por WhatsApp.</p>

    <form class="caja-mesa" method="post" action="<?= e(url('mesa')) ?>">
        <?= csrf_campo() ?>
        <span class="caja-mesa-icono" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22"><path d="M6 10h12M7 10v9M17 10v9M5 19h14M9 10V6h6v4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </span>
        <div class="caja-mesa-texto">
            <strong>¿Estás en la heladería?</strong>
            <span>Elige tu mesa y te llevamos el pedido.</span>
        </div>
        <select name="mesa" aria-label="Elige tu mesa">
            <option value="">Para llevar / domicilio</option>
            <?php foreach ($mesas as $m): ?>
                <option value="<?= e($m['nombre']) ?>" <?= mesa_actual() === $m['nombre'] ? 'selected' : '' ?>><?= e($m['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-rosa" type="submit">Guardar</button>
    </form>
</section>

<section class="bloque bloque-menu bloque-lista" aria-labelledby="titulo-productos">
    <h2 id="titulo-productos" class="titulo-seccion">Todos los productos</h2>
    <p class="subtitulo">Pulsa el carrito de cada producto para agregarlo.</p>

    <form class="buscador" method="get" action="index.php">
        <input type="hidden" name="r" value="menu">
        <input type="search" name="q" value="<?= e($texto) ?>" placeholder="Buscar por nombre o sabor (ej: arequipe)" aria-label="Buscar en el menú">
        <select name="cat" aria-label="Categoría">
            <option value="">Todas las categorías</option>
            <?php foreach ($categorias as $nombreCat): ?>
                <option value="<?= e($nombreCat) ?>" <?= $categoria === $nombreCat ? 'selected' : '' ?>><?= e($nombreCat) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-rosa" type="submit">Buscar</button>
        <?php if ($texto !== '' || $categoria !== ''): ?>
            <a class="btn btn-borde" href="<?= e(url('menu')) ?>">Limpiar</a>
        <?php endif; ?>
    </form>

    <?php if ($grupos === []): ?>
        <div class="vacio-caja">
            <p><?= $categorias === [] ? 'Por ahora no hay productos disponibles. Vuelve en un momento.' : 'No encontramos productos con esa búsqueda.' ?></p>
        </div>
    <?php else: ?>
        <p class="suave"><?= $total ?> <?= $total === 1 ? 'producto' : 'productos' ?></p>
    <?php endif; ?>

    <?php foreach ($grupos as $nombreCat => $productos): ?>
        <h2 class="titulo-categoria"><?= e($nombreCat) ?></h2>
        <div class="rejilla">
            <?php foreach ($productos as $producto): ?>
                <?php require __DIR__ . '/../parciales/producto_tarjeta.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</section>
