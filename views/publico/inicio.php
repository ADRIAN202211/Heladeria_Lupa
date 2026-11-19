<?php $seccion = 'inicio'; ?>
<section class="hero">
    <div class="hero-texto">
        <p class="hero-kicker">Bienvenidos a la heladería</p>
        <h1>Helados <span class="rosa">de verdad</span></h1>
        <p class="hero-sub">Pide desde tu celular o desde tu mesa. Armas el pedido, eliges cómo recibirlo y lo envías por WhatsApp.</p>
        <div class="hero-botones">
            <a class="btn btn-rosa btn-grande" href="<?= e(url('menu')) ?>">Pedir ahora <span aria-hidden="true">→</span></a>
            <a class="btn btn-borde" href="<?= e(url('carrito')) ?>">Ver mi carrito</a>
        </div>
    </div>
    <div class="hero-imagen" aria-hidden="true">
        <img class="hero-foto" src="assets/productos/foto-bowl-chocolate.png" alt="">
    </div>
</section>

<section class="bloque como-pedir">
    <div class="pasos-hero">
        <div><span>1</span><p>Elige tus favoritos del menú.</p></div>
        <div><span>2</span><p>Agrégalos al carrito y completa tus datos.</p></div>
        <div><span>3</span><p>Envía el pedido por WhatsApp.</p></div>
    </div>
</section>

<?php if ($destacados): ?>
<section class="bloque">
    <h2 class="titulo-seccion">Lo más pedido</h2>
    <div class="rejilla">
        <?php foreach ($destacados as $producto): ?>
            <?php require __DIR__ . '/../parciales/producto_tarjeta.php'; ?>
        <?php endforeach; ?>
    </div>
    <p class="centro"><a class="btn btn-borde" href="<?= e(url('menu')) ?>">Ver el menú completo</a></p>
</section>
<?php endif; ?>
