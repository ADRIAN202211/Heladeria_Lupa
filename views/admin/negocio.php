<h1>Negocio y promoción</h1>
<p class="subtitulo">Estos datos aparecen en la página Información y en el pie de página del sitio. Los campos vacíos muestran "Por completar".</p>

<form class="formulario formulario-ancho" method="post" action="<?= e(url('admin/negocio')) ?>">
    <?= csrf_campo() ?>
    <label>Nombre del negocio
        <input type="text" name="negocio_nombre" value="<?= e(ajuste('negocio_nombre')) ?>" maxlength="80" required>
    </label>
    <label>Sobre nosotros (texto libre, aparece en Información)
        <textarea name="sobre_nosotros" rows="6" maxlength="1500"><?= e(ajuste('sobre_nosotros')) ?></textarea>
    </label>
    <label>Dirección
        <input type="text" name="direccion" value="<?= e(ajuste('direccion')) ?>" maxlength="160">
    </label>
    <label>Horario
        <input type="text" name="horario" value="<?= e(ajuste('horario')) ?>" maxlength="120">
    </label>
    <div class="fila">
        <label>Teléfono
            <input type="text" name="telefono" value="<?= e(ajuste('telefono')) ?>" maxlength="40">
        </label>
        <label>Correo
            <input type="email" name="correo" value="<?= e(ajuste('correo')) ?>" maxlength="120">
        </label>
    </div>
    <button class="btn btn-rosa" type="submit">Guardar datos</button>
</form>

<div class="nota-caja">
    <strong>Promociones:</strong> esta sección queda pendiente. El manual indica que se completará cuando el equipo confirme su funcionamiento.
</div>
