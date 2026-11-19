<div class="encabezado-panel">
    <div>
        <h1>Pedidos</h1>
        <p class="subtitulo">Los pedidos se guardan aquí cuando el cliente los envía por WhatsApp.</p>
    </div>
    <a class="btn btn-borde" href="<?= e(url('admin/pedidos/csv')) ?>">Descargar CSV</a>
</div>

<div class="tarjetas-resumen">
    <div class="tarjeta-dato"><span>Pedidos hoy</span><strong><?= $resumen['pedidos'] ?></strong></div>
    <div class="tarjeta-dato"><span>Vendido hoy</span><strong><?= e(dinero($resumen['total'])) ?></strong></div>
    <div class="tarjeta-dato"><span>Pendientes</span><strong><?= $resumen['pendientes'] ?></strong></div>
</div>

<form class="filtro" method="get" action="index.php">
    <input type="hidden" name="r" value="admin/pedidos">
    <label>Estado
        <select name="estado" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php foreach (ESTADOS_PEDIDO as $clave => $texto): ?>
                <option value="<?= e($clave) ?>" <?= $estado === $clave ? 'selected' : '' ?>><?= e($texto) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</form>

<div class="tabla-wrap">
<table class="tabla">
    <thead>
        <tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Entrega</th><th class="derecha">Total</th><th>Estado</th><th></th></tr>
    </thead>
    <tbody>
    <?php if ($pedidos === []): ?>
        <tr><td colspan="7" class="centro">Todavía no hay pedidos<?= $estado !== '' ? ' con ese estado' : '' ?>.</td></tr>
    <?php endif; ?>
    <?php foreach ($pedidos as $p): ?>
        <tr>
            <td>#<?= (int) $p['id'] ?></td>
            <td><?= e($p['creado_en']) ?></td>
            <td><strong><?= e($p['nombre']) ?></strong><div class="suave"><?= e($p['telefono']) ?></div></td>
            <td><?= e($p['entrega']) ?><?= $p['mesa'] !== '' ? ' · ' . e($p['mesa']) : '' ?></td>
            <td class="derecha precio-tabla"><?= e(dinero((int) $p['total'])) ?></td>
            <td><span class="estado estado-<?= e($p['estado']) ?>"><?= e(ESTADOS_PEDIDO[$p['estado']] ?? $p['estado']) ?></span></td>
            <td class="acciones-tabla"><a class="btn-mini btn-editar" href="<?= e(url('admin/pedido', ['id' => $p['id']])) ?>">Ver</a></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
