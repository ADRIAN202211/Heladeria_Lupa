<h1>Categorías</h1>
<p class="subtitulo">Agrupan los productos en el menú del cliente. Una categoría con productos no se puede borrar.</p>

<div class="dos-columnas">
    <div class="tabla-wrap">
        <table class="tabla">
            <thead><tr><th>Nombre</th><th class="derecha">Productos</th><th class="derecha">Acciones</th></tr></thead>
            <tbody>
            <?php if ($categorias === []): ?>
                <tr><td colspan="3" class="centro">No hay categorías todavía.</td></tr>
            <?php endif; ?>
            <?php foreach ($categorias as $c): ?>
                <tr>
                    <td><strong><?= e($c['nombre']) ?></strong></td>
                    <td class="derecha"><?= (int) $c['total'] ?></td>
                    <td class="derecha">
                        <form method="post" action="<?= e(url('admin/categorias/eliminar')) ?>" data-confirm="¿Eliminar la categoría «<?= e($c['nombre']) ?>»?">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                            <button class="btn-mini btn-borrar" type="submit" <?= (int) $c['total'] > 0 ? 'disabled title="Tiene productos"' : '' ?>>Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <form class="formulario" method="post" action="<?= e(url('admin/categorias')) ?>">
        <?= csrf_campo() ?>
        <h2>Nueva categoría</h2>
        <label>Nombre
            <input type="text" name="nombre" maxlength="60" placeholder="Ej: Malteadas" required>
        </label>
        <button class="btn btn-rosa btn-bloque" type="submit">Crear categoría</button>
    </form>
</div>
