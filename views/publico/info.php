<?php
$seccion = 'info';
$sobre = ajuste('sobre_nosotros');
$direccion = ajuste('direccion');
$horario = ajuste('horario');
$telefono = ajuste('telefono');
$correo = ajuste('correo');
$whatsapp = preg_replace('/\D+/', '', ajuste('whatsapp'));
?>
<section class="bloque">
    <h1 class="titulo-pagina">Información</h1>

    <div class="info-grid">
        <article class="caja">
            <h2>Sobre nosotros</h2>
            <?php if ($sobre !== ''): ?>
                <p class="texto-libre"><?= nl2br(e($sobre)) ?></p>
            <?php else: ?>
                <p class="suave">Esta sección se completa desde el panel, en Negocio y promoción.</p>
            <?php endif; ?>
        </article>

        <article class="caja">
            <h2>Dónde estamos y cuándo</h2>
            <dl class="datos">
                <dt>Dirección</dt><dd><?= $direccion !== '' ? e($direccion) : '<span class="suave">Por completar</span>' ?></dd>
                <dt>Horario</dt><dd><?= $horario !== '' ? e($horario) : '<span class="suave">Por completar</span>' ?></dd>
                <dt>Teléfono</dt><dd><?= $telefono !== '' ? e($telefono) : '<span class="suave">Por completar</span>' ?></dd>
                <dt>Correo</dt><dd><?= $correo !== '' ? e($correo) : '<span class="suave">Por completar</span>' ?></dd>
            </dl>
            <?php if ($whatsapp !== ''): ?>
                <a class="btn btn-whatsapp" href="https://wa.me/<?= e($whatsapp) ?>">Escríbenos por WhatsApp</a>
            <?php endif; ?>
        </article>
    </div>

    <h2 class="titulo-seccion">Cómo pedir</h2>
    <ol class="pasos">
        <li><strong>Elige</strong> tus productos en el menú y pulsa el carrito de cada uno.</li>
        <li><strong>Revisa</strong> el carrito. Puedes cambiar cantidades o quitar productos.</li>
        <li><strong>Completa</strong> tus datos y elige domicilio, recoger en tienda o en tu mesa.</li>
        <li><strong>Envía</strong> el pedido por WhatsApp. El negocio te confirma el pedido y el tiempo de entrega.</li>
    </ol>

    <h2 class="titulo-seccion">Preguntas frecuentes</h2>
    <div class="faq">
        <details><summary>¿Necesito cuenta para pedir?</summary><p>No. Solo llenas el formulario del pedido.</p></details>
        <details><summary>¿Cómo se paga?</summary><p>Al recibir el pedido o por transferencia. El negocio te lo confirma por WhatsApp.</p></details>
        <details><summary>¿Puedo pedir estando en la heladería?</summary><p>Sí. Elige "En mi mesa" y escanea el código QR de tu mesa para que el pedido llegue con el número de mesa.</p></details>
        <details><summary>¿Qué pasa si un producto se agota?</summary><p>Deja de aparecer en el menú. Si lo tenías en el carrito, se quita y el sitio te avisa.</p></details>
    </div>
</section>
