<?php
$editando = !empty($producto['id']);
$v = fn (string $campo, string $porDefecto = ''): string => e($producto[$campo] ?? $porDefecto);
?>
<h1><?= $editando ? 'Editar producto' : 'Nuevo producto' ?></h1>

<form class="formulario formulario-ancho" method="post" action="<?= e(url('admin/producto')) ?>" enctype="multipart/form-data">
    <?= csrf_campo() ?>
    <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
    <?php endif; ?>

    <div class="fila">
        <label>Nombre *
            <input type="text" name="nombre" value="<?= $v('nombre') ?>" maxlength="100" required>
        </label>
        <label>Categoría
            <select name="categoria_id">
                <option value="">Sin categoría</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (int) ($producto['categoria_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <label>Descripción
        <textarea name="descripcion" rows="3" maxlength="300"><?= $v('descripcion') ?></textarea>
    </label>

    <div class="fila">
        <label>Precio (pesos) *
            <input type="number" name="precio" value="<?= $v('precio') ?>" min="1" step="50" required>
        </label>
        <label>Etiqueta
            <select name="etiqueta">
                <?php foreach (['' => 'Ninguna', 'Favorito' => 'Favorito', 'Nuevo' => 'Nuevo'] as $valor => $texto): ?>
                    <option value="<?= e($valor) ?>" <?= ($producto['etiqueta'] ?? '') === $valor ? 'selected' : '' ?>><?= e($texto) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <div class="fila">
        <label>Imagen (JPG, PNG o WEBP, máx. 2 MB)
            <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp">
        </label>
        <div class="previa-foto">
            <?php if (!empty($producto['imagen'])): ?>
                <img src="<?= e($producto['imagen']) ?>" alt="Imagen actual">
                <span class="suave">Imagen actual. Sube otra para reemplazarla.</span>
            <?php else: ?>
                <span class="suave">Sin imagen: el cliente verá un cono de ejemplo.</span>
            <?php endif; ?>
        </div>
    </div>

    <label class="casilla">
        <input type="checkbox" name="disponible" value="1" <?= !isset($producto['disponible']) || (int) $producto['disponible'] ? 'checked' : '' ?>>
        Disponible (si no está marcado, no se vende)
    </label>

    <div class="botones-form">
        <button class="btn btn-rosa" type="submit">Guardar</button>
        <a class="btn btn-borde" href="<?= e(url('admin/productos')) ?>">Cancelar</a>
    </div>
</form>
