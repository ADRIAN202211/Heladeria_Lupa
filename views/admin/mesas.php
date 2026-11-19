<h1>Mesas y códigos QR</h1>
<p class="subtitulo">Cada mesa tiene su QR. Al escanearlo, el menú se abre con esa mesa ya seleccionada.</p>

<div class="dos-columnas">
    <div>
        <form class="formulario" method="post" action="<?= e(url('admin/mesas')) ?>">
            <?= csrf_campo() ?>
            <label>Mesas (una por línea)
                <textarea name="mesas" rows="12"><?= e(implode("\n", array_column($mesas, 'nombre'))) ?></textarea>
            </label>
            <p class="suave">Puedes usar nombres como «Mesa 5», «Barra» o «Terraza 2».</p>
            <button class="btn btn-rosa" type="submit">Guardar mesas</button>
        </form>

        <form class="formulario" method="post" action="<?= e(url('admin/mesas/generar')) ?>">
            <?= csrf_campo() ?>
            <h2>Generar rápido</h2>
            <div class="fila-inline">
                <input type="number" name="cantidad" value="12" min="1" max="100" aria-label="Cantidad de mesas">
                <button class="btn btn-borde" type="submit">Crear «Mesa 1» a «Mesa N»</button>
            </div>
        </form>
    </div>

    <div>
        <p class="suave">Los QR usan la dirección actual del sitio: <code><?= e(explode('?', url_absoluta([]))[0]) ?></code>. Si cambias el dominio al publicar, genéralos de nuevo.</p>
        <div class="rejilla-qr">
            <?php foreach ($mesas as $mesa): ?>
                <div class="tarjeta-qr">
                    <canvas data-qr="<?= e($mesa['enlace']) ?>" width="180" height="180" aria-label="QR de <?= e($mesa['nombre']) ?>"></canvas>
                    <strong><?= e($mesa['nombre']) ?></strong>
                    <a href="<?= e($mesa['enlace']) ?>" target="_blank" rel="noopener">Abrir enlace</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="assets/vendor/qrcode.min.js"></script>
