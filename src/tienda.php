<?php
declare(strict_types=1);

/* ---------- Categorías ---------- */

function categorias_listar(): array
{
    return db()->query(
        'SELECT c.*, (SELECT COUNT(*) FROM productos p WHERE p.categoria_id = c.id) AS total
         FROM categorias c ORDER BY c.orden, c.nombre'
    )->fetchAll();
}

function categoria_crear(string $nombre): void
{
    $nombre = trim($nombre);
    if ($nombre === '') {
        throw new InvalidArgumentException('Escribe un nombre para la categoría.');
    }
    $orden = (int) db()->query('SELECT COALESCE(MAX(orden), 0) + 1 FROM categorias')->fetchColumn();
    try {
        db()->prepare('INSERT INTO categorias (nombre, orden) VALUES (?, ?)')->execute([$nombre, $orden]);
    } catch (PDOException) {
        throw new InvalidArgumentException('Ya existe una categoría con ese nombre.');
    }
}

function categoria_eliminar(int $id): void
{
    $st = db()->prepare('SELECT COUNT(*) FROM productos WHERE categoria_id = ?');
    $st->execute([$id]);
    if ((int) $st->fetchColumn() > 0) {
        throw new InvalidArgumentException('No se puede borrar: tiene productos. Cámbialos de categoría primero.');
    }
    db()->prepare('DELETE FROM categorias WHERE id = ?')->execute([$id]);
}

/* ---------- Productos ---------- */

function productos_listar(bool $soloDisponibles = false): array
{
    $sql = 'SELECT p.*, c.nombre AS categoria
            FROM productos p LEFT JOIN categorias c ON c.id = p.categoria_id';
    if ($soloDisponibles) {
        $sql .= ' WHERE p.disponible = 1';
    }
    $sql .= ' ORDER BY p.orden, p.id';
    return db()->query($sql)->fetchAll();
}

function producto_obtener(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM productos WHERE id = ?');
    $st->execute([$id]);
    $fila = $st->fetch();
    return $fila ?: null;
}

/**
 * Crea o actualiza un producto.
 * En actualización, $imagen null conserva la imagen actual.
 */
function producto_guardar(array $datos, ?int $id, ?string $imagen): int
{
    $nombre = trim((string) ($datos['nombre'] ?? ''));
    $descripcion = mb_substr(trim((string) ($datos['descripcion'] ?? '')), 0, 300);
    $precio = (int) preg_replace('/\D+/', '', (string) ($datos['precio'] ?? ''));
    $categoriaId = (int) ($datos['categoria_id'] ?? 0) ?: null;
    $etiqueta = (string) ($datos['etiqueta'] ?? '');
    $disponible = empty($datos['disponible']) ? 0 : 1;

    if ($nombre === '') {
        throw new InvalidArgumentException('El producto necesita un nombre.');
    }
    if ($precio <= 0) {
        throw new InvalidArgumentException('Escribe un precio mayor a cero.');
    }
    if (!in_array($etiqueta, ['', 'Favorito', 'Nuevo'], true)) {
        throw new InvalidArgumentException('Etiqueta no válida.');
    }

    $pdo = db();
    if ($id === null) {
        $orden = (int) $pdo->query('SELECT COALESCE(MAX(orden), 0) + 1 FROM productos')->fetchColumn();
        $st = $pdo->prepare(
            'INSERT INTO productos (categoria_id, nombre, descripcion, precio, imagen, etiqueta, disponible, orden)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $st->execute([$categoriaId, $nombre, $descripcion, $precio, $imagen ?? '', $etiqueta, $disponible, $orden]);
        return (int) $pdo->lastInsertId();
    }

    $actual = producto_obtener($id);
    if ($actual === null) {
        throw new InvalidArgumentException('El producto ya no existe.');
    }
    $imagenFinal = $imagen ?? $actual['imagen'];
    $st = $pdo->prepare(
        'UPDATE productos SET categoria_id = ?, nombre = ?, descripcion = ?, precio = ?,
                imagen = ?, etiqueta = ?, disponible = ? WHERE id = ?'
    );
    $st->execute([$categoriaId, $nombre, $descripcion, $precio, $imagenFinal, $etiqueta, $disponible, $id]);
    if ($imagen !== null && $actual['imagen'] !== '' && $actual['imagen'] !== $imagen) {
        imagen_borrar($actual['imagen']);
    }
    return $id;
}

function producto_alternar_disponible(int $id): void
{
    db()->prepare('UPDATE productos SET disponible = 1 - disponible WHERE id = ?')->execute([$id]);
}

function producto_eliminar(int $id): void
{
    $actual = producto_obtener($id);
    if ($actual === null) {
        return;
    }
    db()->prepare('DELETE FROM productos WHERE id = ?')->execute([$id]);
    imagen_borrar($actual['imagen']);
}

/** Sube un producto una posición ('subir') o lo baja ('bajar') en el orden del menú. */
function producto_mover(int $id, string $direccion): void
{
    $ids = array_map('intval', db()->query('SELECT id FROM productos ORDER BY orden, id')->fetchAll(PDO::FETCH_COLUMN));
    $pos = array_search($id, $ids, true);
    if ($pos === false) {
        return;
    }
    $otra = $direccion === 'subir' ? $pos - 1 : $pos + 1;
    if ($otra < 0 || $otra >= count($ids)) {
        return;
    }
    [$ids[$pos], $ids[$otra]] = [$ids[$otra], $ids[$pos]];

    $pdo = db();
    $pdo->beginTransaction();
    $st = $pdo->prepare('UPDATE productos SET orden = ? WHERE id = ?');
    foreach ($ids as $i => $pid) {
        $st->execute([($i + 1) * 10, $pid]);
    }
    $pdo->commit();
}

/** Valida y guarda una imagen subida. Devuelve la ruta relativa a public/ o null si no se subió nada. */
function imagen_guardar(array $archivo): ?string
{
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($archivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('No se pudo subir la imagen. Inténtalo otra vez.');
    }
    if ($archivo['size'] > config()['max_upload_bytes']) {
        throw new InvalidArgumentException('La imagen supera 2 MB.');
    }
    $info = @getimagesize($archivo['tmp_name']);
    $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = $info['mime'] ?? '';
    if (!isset($tipos[$mime])) {
        throw new InvalidArgumentException('La imagen debe ser JPG, PNG o WEBP.');
    }

    $dir = config()['uploads_dir'];
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $nombre = bin2hex(random_bytes(8)) . '.' . $tipos[$mime];
    if (!move_uploaded_file($archivo['tmp_name'], $dir . '/' . $nombre)) {
        throw new RuntimeException('No se pudo guardar la imagen en el servidor.');
    }
    return 'uploads/' . $nombre;
}

/** Borra una imagen subida por el panel (solo dentro de public/uploads). */
function imagen_borrar(string $ruta): void
{
    if (!str_starts_with($ruta, 'uploads/')) {
        return;
    }
    $archivo = config()['uploads_dir'] . '/' . basename($ruta);
    if (is_file($archivo)) {
        unlink($archivo);
    }
}

/* ---------- Mesas ---------- */

function mesas_listar(): array
{
    return db()->query('SELECT * FROM mesas ORDER BY orden, id')->fetchAll();
}

/** Reemplaza la lista de mesas con el texto (una mesa por línea). */
function mesas_reemplazar(string $texto): int
{
    $lineas = array_values(array_unique(array_filter(array_map(
        static fn (string $l): string => mb_substr(trim($l), 0, 40),
        preg_split('/\R/', $texto) ?: []
    ), static fn (string $l): bool => $l !== '')));

    if (!$lineas) {
        throw new InvalidArgumentException('Escribe al menos una mesa.');
    }

    $pdo = db();
    $pdo->beginTransaction();
    $pdo->exec('DELETE FROM mesas');
    $st = $pdo->prepare('INSERT INTO mesas (nombre, orden) VALUES (?, ?)');
    foreach ($lineas as $i => $nombre) {
        $st->execute([$nombre, ($i + 1) * 10]);
    }
    $pdo->commit();
    return count($lineas);
}

/** Crea "Mesa 1" a "Mesa N" sin tocar las que ya existen. */
function mesas_generar(int $n): int
{
    if ($n < 1 || $n > 100) {
        throw new InvalidArgumentException('Escribe un número entre 1 y 100.');
    }
    $st = db()->prepare('INSERT OR IGNORE INTO mesas (nombre, orden) VALUES (?, ?)');
    for ($i = 1; $i <= $n; $i++) {
        $st->execute(['Mesa ' . $i, $i * 10]);
    }
    return $n;
}

/**
 * Filtra productos por texto (nombre o descripción, sin importar mayúsculas)
 * y por categoría. Trabaja sobre la lista que ya trae productos_listar().
 */
function productos_filtrar(array $productos, string $texto, string $categoria): array
{
    $texto = mb_strtolower(trim($texto));
    return array_values(array_filter($productos, static function (array $p) use ($texto, $categoria): bool {
        if ($categoria !== '' && ($p['categoria'] ?? '') !== $categoria) {
            return false;
        }
        if ($texto === '') {
            return true;
        }
        $busqueda = mb_strtolower($p['nombre'] . ' ' . $p['descripcion']);
        return mb_strpos($busqueda, $texto) !== false;
    }));
}
