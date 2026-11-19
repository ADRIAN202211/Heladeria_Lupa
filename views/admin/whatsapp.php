<h1>WhatsApp y mensaje del pedido</h1>
<p class="subtitulo">Cuando un cliente toca «Enviar pedido por WhatsApp», se abre un chat con este número y este mensaje ya escrito.</p>

<div class="dos-columnas">
    <form class="formulario" method="post" action="<?= e(url('admin/whatsapp')) ?>">
        <?= csrf_campo() ?>

        <div class="fila">
            <label>Número de WhatsApp
                <input type="text" name="whatsapp" value="<?= e(ajuste('whatsapp')) ?>" inputmode="numeric" required>
                <span class="suave">Con código de país y sin espacios. Colombia: 57 + celular (ej. 573245245615).</span>
            </label>
            <label>Costo del domicilio ($)
                <input type="number" name="domicilio_costo" value="<?= e(ajuste('domicilio_costo')) ?>" min="0" step="100">
                <span class="suave">Pon 0 si el domicilio es gratis.</span>
            </label>
        </div>

        <label>Mensaje que se envía
            <textarea name="mensaje_pedido" rows="16" class="mono"><?= e(ajuste('mensaje_pedido')) ?></textarea>
        </label>
        <p class="suave">Las palabras entre llaves se reemplazan solas con los datos del pedido: {negocio}, {pedido}, {subtotal}, {domicilio}, {total}, {nombre}, {telefono}, {entrega}, {direccion}, {pago} y {nota}.</p>

        <button class="btn btn-rosa" type="submit">Guardar cambios</button>
    </form>

    <div class="vista-previa">
        <h2>Vista previa (pedido de ejemplo)</h2>
        <div class="burbuja"><?= e($previa) ?></div>
        <p class="suave">Se muestra con los datos guardados. Guarda para ver los cambios en el envío real.</p>
    </div>
</div>
