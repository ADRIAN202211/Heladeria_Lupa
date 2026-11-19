<?php
$seccion = 'carrito';
$entregaElegida = $datos['entrega'] ?? ($mesa !== null ? 'mesa' : 'domicilio');
$pagoElegido = $datos['pago'] ?? 'Efectivo';
$error = fn (string $campo): string => isset($errores[$campo])
    ? '<span class="error-campo">' . e($errores[$campo]) . '</span>' : '';
$costoInicial = $entregaElegida === 'domicilio' ? $costoDomicilio : 0;
?>
<section class="bloque">
    <h1 class="titulo-pagina">Datos para tu pedido</h1>
    <p class="subtitulo">Los campos con * son obligatorios.</p>

    <div class="dos-columnas">
        <form class="formulario" id="form-pedido" method="post" action="<?= e(url('pedido')) ?>" novalidate>
            <?= csrf_campo() ?>

            <div class="fila">
                <label>Nombre *
                    <input type="text" name="nombre" value="<?= e($datos['nombre'] ?? '') ?>" maxlength="80" required>
                    <?= $error('nombre') ?>
                </label>
                <label>Teléfono / celular *
                    <input type="tel" name="telefono" value="<?= e($datos['telefono'] ?? '') ?>" maxlength="25" required>
                    <?= $error('telefono') ?>
                </label>
            </div>

            <fieldset class="entregas">
                <legend>¿Cómo quieres recibirlo? *</legend>
                <?= $error('entrega') ?>
                <label class="opcion">
                    <input type="radio" name="entrega" value="domicilio" data-costo="<?= $costoDomicilio ?>" <?= $entregaElegida === 'domicilio' ? 'checked' : '' ?>>
                    <span class="opcion-titulo">Domicilio</span>
                    <span class="opcion-detalle">+<?= e(dinero($costoDomicilio)) ?></span>
                </label>
                <label class="opcion">
                    <input type="radio" name="entrega" value="recoger" data-costo="0" <?= $entregaElegida === 'recoger' ? 'checked' : '' ?>>
                    <span class="opcion-titulo">Recoger en tienda</span>
                    <span class="opcion-detalle">Gratis</span>
                </label>
                <label class="opcion">
                    <input type="radio" name="entrega" value="mesa" data-costo="0" <?= $entregaElegida === 'mesa' ? 'checked' : '' ?>>
                    <span class="opcion-titulo">En mi mesa</span>
                    <span class="opcion-detalle"><?= $mesa !== null ? e($mesa) : 'Escanea el QR de tu mesa' ?></span>
                </label>
            </fieldset>

            <div id="bloque-domicilio" class="fila" <?= $entregaElegida === 'domicilio' ? '' : 'hidden' ?>>
                <label>Dirección *
                    <input type="text" name="direccion" value="<?= e($datos['direccion'] ?? '') ?>" maxlength="120">
                    <?= $error('direccion') ?>
                </label>
                <label>Barrio *
                    <input type="text" name="barrio" value="<?= e($datos['barrio'] ?? '') ?>" maxlength="60">
                    <?= $error('barrio') ?>
                </label>
            </div>

            <label>Método de pago *
                <select name="pago">
                    <?php foreach (['Efectivo', 'Nequi', 'Transferencia'] as $metodo): ?>
                        <option value="<?= e($metodo) ?>" <?= $pagoElegido === $metodo ? 'selected' : '' ?>><?= e($metodo) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= $error('pago') ?>
            </label>

            <label>Notas (opcional)
                <textarea name="nota" rows="3" maxlength="300" placeholder="Ej: Sin crema"><?= e($datos['nota'] ?? '') ?></textarea>
            </label>

            <p class="volver-link"><a href="<?= e(url('carrito')) ?>">← Volver al carrito</a></p>
        </form>

        <aside class="resumen">
            <h2>Resumen del pedido</h2>
            <dl>
                <dt>Subtotal</dt>
                <dd data-subtotal="<?= $totales['subtotal'] ?>"><?= e(dinero($totales['subtotal'])) ?></dd>
                <dt>Domicilio</dt>
                <dd id="resumen-domicilio"><?= $costoInicial > 0 ? e(dinero($costoInicial)) : 'No aplica' ?></dd>
            </dl>
            <p class="total">
                <span>Total a pagar</span>
                <strong id="resumen-total"><?= e(dinero($totales['subtotal'] + $costoInicial)) ?></strong>
            </p>
            <button class="btn btn-whatsapp btn-bloque" type="submit" form="form-pedido">Enviar pedido por WhatsApp</button>
            <p class="nota-pago">Pagas al recibir o por transferencia. Te confirmamos el pedido y el tiempo de entrega por WhatsApp.</p>
        </aside>
    </div>
</section>
