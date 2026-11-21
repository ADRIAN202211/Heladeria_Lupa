<?php
$seccion = 'contacto';
$direccion = ajuste('direccion');
$horario = ajuste('horario');
$telefono = ajuste('telefono');
$correo = ajuste('correo');
?>
<section class="bloque">
    <h1 class="titulo-pagina">Contacto</h1>

    <div class="info-grid">
        <article class="caja">
            <h2>Escríbenos</h2>
            <form class="formulario" method="post" action="<?= e(url('contacto')) ?>">
                <?= csrf_campo() ?>
                <label>Tu nombre *
                    <input type="text" name="nombre" maxlength="80" required>
                </label>
                <label>Mensaje *
                    <textarea name="mensaje" rows="4" maxlength="500" required></textarea>
                </label>
                <button class="btn btn-whatsapp" type="submit">Enviar por WhatsApp</button>
            </form>
            <p class="suave">Se abre WhatsApp con tu mensaje listo para enviar.</p>
        </article>

        <article class="caja">
            <h2>Datos de contacto</h2>
            <dl class="datos">
                <dt>Dirección</dt><dd><?= $direccion !== '' ? e($direccion) : '<span class="suave">Por completar</span>' ?></dd>
                <dt>Horario</dt><dd><?= $horario !== '' ? e($horario) : '<span class="suave">Por completar</span>' ?></dd>
                <dt>Teléfono</dt><dd><?= $telefono !== '' ? e($telefono) : '<span class="suave">Por completar</span>' ?></dd>
                <dt>Correo</dt><dd><?= $correo !== '' ? e($correo) : '<span class="suave">Por completar</span>' ?></dd>
            </dl>
            <?php if ($direccion !== ''): ?>
                <p><a class="enlace" href="https://www.google.com/maps/search/?api=1&amp;query=<?= e(rawurlencode($direccion)) ?>" target="_blank" rel="noopener">Ver en el mapa</a></p>
            <?php endif; ?>
        </article>
    </div>
</section>
