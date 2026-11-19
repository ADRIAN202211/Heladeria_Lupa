<?php
/**
 * Tarjeta de producto para el cliente. Espera la variable $producto.
 * Usa el formulario de carrito: agregar + volver a la página de origen.
 */
$volver = $seccion ?? 'menu';
?>
<article class="tarjeta">
    <div class="tarjeta-foto">
        <?php if (!empty($producto['etiqueta'])): ?>
            <span class="etiqueta etiqueta-<?= strtolower(e($producto['etiqueta'])) ?>"><?= e($producto['etiqueta']) ?></span>
        <?php endif; ?>
        <?php if ($producto['imagen'] !== ''): ?>
            <img src="<?= e($producto['imagen']) ?>" alt="<?= e($producto['nombre']) ?>" loading="lazy">
        <?php else: ?>
            <?php require __DIR__ . '/cono.php'; ?>
        <?php endif; ?>
    </div>
    <div class="tarjeta-cuerpo">
        <p class="categoria"><?= e(mb_strtoupper((string) ($producto['categoria'] ?? 'Otros'))) ?></p>
        <h3><?= e($producto['nombre']) ?></h3>
        <p class="descripcion"><?= e($producto['descripcion']) ?></p>
        <div class="tarjeta-pie">
            <span class="precio"><?= e(dinero((int) $producto['precio'])) ?></span>
            <form method="post" action="<?= e(url('carrito')) ?>">
                <?= csrf_campo() ?>
                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
                <input type="hidden" name="volver" value="<?= e($volver) ?>">
                <button class="boton-carrito" type="submit" aria-label="Agregar <?= e($producto['nombre']) ?> al carrito">
                    <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M3 4h2l2.4 10.2a1.5 1.5 0 0 0 1.5 1.2h7.9a1.5 1.5 0 0 0 1.5-1.1L20 8H6.2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="19.5" r="1.3" fill="currentColor"/><circle cx="17" cy="19.5" r="1.3" fill="currentColor"/></svg>
                </button>
            </form>
        </div>
    </div>
</article>
