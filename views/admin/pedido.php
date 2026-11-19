<?php $p = $pedido; ?>
<div class="encabezado-panel">
    <div>
        <h1>Pedido #<?= (int) $p['id'] ?></h1>
        <p class="subtitulo"><?= e($p['creado_en']) ?></p>
    </div>
    <a class="btn btn-borde" href="<?= e(url('admin/pedidos')) ?>">← Volver a pedidos</a>
</div>

<div class="dos-columnas">
    <article class="caja">
        <h2>Cliente y entrega</h2>
        <dl class="datos">
            <dt>Nombre</dt><dd><?= e($p['nombre']) ?></dd>
            <dt>Teléfono</dt><dd><?= e($p['telefono']) ?></dd>
            <dt>Entrega</dt><dd><?= e($p['entrega']) ?><?= $p['mesa'] !== '' ? ' · ' . e($p['mesa']) : '' ?></dd>
            <?php if ($p['direccion'] !== ''): ?>
                <dt>Dirección</dt><dd><?= e($p['direccion']) ?>, <?= e($p['barrio']) ?></dd>
            <?php endif; ?>
            <dt>Pago</dt><dd><?= e($p['pago']) ?></dd>
            <dt>Nota</dt><dd><?= $p['nota'] !== '' ? e($p['nota']) : '<span class="suave">Ninguna</span>' ?></dd>
        </dl>
        <p><a class="btn btn-whatsapp" href="https://wa.me/<?= e(preg_replace('/\D+/', '', $p['telefono'])) ?>">Escribir al cliente</a></p>
    </article>

    <article class="caja">
        <h2>Productos</h2>
        <table class="tabla tabla-simple">
            <tbody>
            <?php foreach ($p['lineas'] as $l): ?>
                <tr>
                    <td><?= (int) $l['cantidad'] ?> × <?= e($l['nombre']) ?></td>
                    <td class="derecha"><?= e(dinero((int) $l['linea'])) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr><td>Subtotal</td><td class="derecha"><?= e(dinero((int) $p['subtotal'])) ?></td></tr>
            <tr><td>Domicilio</td><td class="derecha"><?= (int) $p['domicilio'] > 0 ? e(dinero((int) $p['domicilio'])) : 'No aplica' ?></td></tr>
            <tr class="total-fila"><td><strong>Total</strong></td><td class="derecha"><strong><?= e(dinero((int) $p['total'])) ?></strong></td></tr>
            </tbody>
        </table>

        <form class="formulario" method="post" action="<?= e(url('admin/pedido/estado')) ?>">
            <?= csrf_campo() ?>
            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
            <label>Estado del pedido
                <select name="estado">
                    <?php foreach (ESTADOS_PEDIDO as $clave => $texto): ?>
                        <option value="<?= e($clave) ?>" <?= $p['estado'] === $clave ? 'selected' : '' ?>><?= e($texto) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn btn-rosa" type="submit">Actualizar estado</button>
        </form>
    </article>
</div>
