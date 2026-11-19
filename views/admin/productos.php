<div class="encabezado-panel">
    <div>
        <h1>Productos del menú</h1>
        <p class="subtitulo"><?= count($productos) ?> productos · el orden de esta lista es el orden en el menú.</p>
    </div>
    <a class="btn btn-rosa" href="<?= e(url('admin/producto')) ?>">+ Nuevo producto</a>
</div>

<div class="tabla-wrap">
<table class="tabla">
    <thead>
        <tr>
            <th></th>
            <th>Producto</th>
            <th>Categoría</th>
            <th class="derecha">Precio</th>
            <th class="centro">Estado</th>
            <th class="derecha">Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($productos === []): ?>
        <tr><td colspan="6" class="centro">Aún no hay productos. Crea el primero.</td></tr>
    <?php endif; ?>
    <?php foreach ($productos as $i => $p): ?>
        <tr>
            <td class="miniatura">
                <?php if ($p['imagen'] !== ''): ?>
                    <img src="<?= e($p['imagen']) ?>" alt="">
                <?php else: ?>
                    <span class="sin-foto">Sin foto</span>
                <?php endif; ?>
            </td>
            <td>
                <strong><?= e($p['nombre']) ?></strong>
                <?php if ($p['etiqueta'] !== ''): ?>
                    <span class="etiqueta-mini"><?= e($p['etiqueta']) ?></span>
                <?php endif; ?>
                <div class="suave"><?= e($p['descripcion']) ?></div>
            </td>
            <td><?= e($p['categoria'] ?? 'Sin categoría') ?></td>
            <td class="derecha precio-tabla"><?= e(dinero((int) $p['precio'])) ?></td>
            <td class="centro">
                <form method="post" action="<?= e(url('admin/producto/disponible')) ?>">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button class="pastilla <?= $p['disponible'] ? 'pastilla-ok' : 'pastilla-no' ?>" type="submit" title="Cambiar disponibilidad">
                        <?= $p['disponible'] ? '● Disponible' : '○ No disponible' ?>
                    </button>
                </form>
            </td>
            <td class="acciones-tabla">
                <form method="post" action="<?= e(url('admin/producto/mover')) ?>">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button class="btn-mini" name="direccion" value="subir" type="submit" <?= $i === 0 ? 'disabled' : '' ?> aria-label="Subir">↑</button>
                    <button class="btn-mini" name="direccion" value="bajar" type="submit" <?= $i === count($productos) - 1 ? 'disabled' : '' ?> aria-label="Bajar">↓</button>
                </form>
                <a class="btn-mini btn-editar" href="<?= e(url('admin/producto', ['id' => $p['id']])) ?>">Editar</a>
                <form method="post" action="<?= e(url('admin/producto/eliminar')) ?>" data-confirm="¿Eliminar este producto? No se puede recuperar.">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                    <button class="btn-mini btn-borrar" type="submit" aria-label="Eliminar">🗑</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
