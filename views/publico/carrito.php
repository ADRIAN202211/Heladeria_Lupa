<?php $seccion = 'carrito'; ?>
<section class="bloque">
    <h1 class="titulo-pagina">Carrito</h1>

    <?php if ($items === []): ?>
        <div class="vacio-caja">
            <p>Tu carrito está vacío.</p>
            <a class="btn btn-rosa" href="<?= e(url('menu')) ?>">Ir al menú</a>
        </div>
    <?php else: ?>
    <div class="dos-columnas">
        <div class="lista-carrito">
            <?php foreach ($items as $item): $p = $item['producto']; ?>
                <div class="linea">
                    <div class="linea-foto">
                        <?php if ($p['imagen'] !== ''): ?>
                            <img src="<?= e($p['imagen']) ?>" alt="<?= e($p['nombre']) ?>">
                        <?php else: ?>
                            <?php require __DIR__ . '/../parciales/cono.php'; ?>
                        <?php endif; ?>
                    </div>

                    <div class="linea-nombre">
                        <strong><?= e($p['nombre']) ?></strong>
                        <span class="precio-unitario"><?= e(dinero((int) $p['precio'])) ?> c/u</span>
                    </div>

                    <div class="cantidad">
                        <form method="post" action="<?= e(url('carrito')) ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="accion" value="cambiar">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" name="cantidad" value="<?= $item['cantidad'] - 1 ?>" aria-label="Quitar uno">−</button>
                        </form>
                        <span class="numero" aria-label="Cantidad"><?= $item['cantidad'] ?></span>
                        <form method="post" action="<?= e(url('carrito')) ?>">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="accion" value="cambiar">
                            <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                            <button type="submit" name="cantidad" value="<?= $item['cantidad'] + 1 ?>" aria-label="Agregar uno">+</button>
                        </form>
                    </div>

                    <span class="linea-total"><?= e(dinero($item['linea'])) ?></span>

                    <form method="post" action="<?= e(url('carrito')) ?>">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="accion" value="quitar">
                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                        <button class="quitar" type="submit" aria-label="Quitar <?= e($p['nombre']) ?>">×</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <form class="vaciar" method="post" action="<?= e(url('carrito')) ?>" data-confirm="¿Vaciar todo el carrito?">
                <?= csrf_campo() ?>
                <input type="hidden" name="accion" value="vaciar">
                <button class="btn btn-borde" type="submit">Vaciar carrito</button>
            </form>
        </div>

        <aside class="resumen">
            <h2>Resumen del pedido</h2>
            <dl>
                <dt>Subtotal (<?= $totales['unidades'] ?> <?= $totales['unidades'] === 1 ? 'producto' : 'productos' ?>)</dt>
                <dd><?= e(dinero($totales['subtotal'])) ?></dd>
                <dt>Domicilio</dt>
                <dd class="suave">Se define al elegir la entrega</dd>
            </dl>
            <p class="total">
                <span>Total</span>
                <strong><?= e(dinero($totales['subtotal'])) ?></strong>
            </p>
            <a class="btn btn-whatsapp btn-bloque" href="<?= e(url('pedido')) ?>">Continuar con mis datos</a>
            <p class="nota-pago">Pagas al recibir o por transferencia. El negocio te confirma el pedido y el tiempo de entrega por WhatsApp.</p>
        </aside>
    </div>
    <?php endif; ?>
</section>
