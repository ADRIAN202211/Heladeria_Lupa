<?php
declare(strict_types=1);

/*
 * Prepara la base de datos local: tablas, menú inicial, mesas y contraseña del panel.
 *
 *   php bin/setup.php                      # genera una contraseña aleatoria y la muestra
 *   php bin/setup.php --clave=mi-clave     # usa la contraseña que indiques
 *   php bin/setup.php --reiniciar          # borra la base y empieza de cero
 *
 * Es seguro ejecutarlo varias veces: no duplica productos ni mesas.
 * Con --clave cambia la contraseña del panel.
 */

if (PHP_SAPI !== 'cli') {
    exit("Ejecuta este script desde la terminal.\n");
}

require __DIR__ . '/../src/bootstrap.php';

$opciones = getopt('', ['clave::', 'reiniciar']);

if (isset($opciones['reiniciar'])) {
    $ruta = config()['db_path'];
    if (is_file($ruta)) {
        unlink($ruta);
        echo "Base anterior borrada.\n";
    }
}

$pdo = db(); // crea las tablas si no existen

// Ajustes iniciales: solo se escriben si no existen, para no pisar lo que el negocio ya cambió.
$iniciales = config()['ajustes'];
$st = $pdo->prepare('INSERT OR IGNORE INTO ajustes (clave, valor) VALUES (?, ?)');
foreach ($iniciales as $clave => $valor) {
    $st->execute([$clave, (string) $valor]);
}

// Menú inicial.
if ((int) $pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn() === 0) {
    $categorias = [];
    foreach (config()['productos_iniciales'] as [$categoria, $nombre, $descripcion, $precio, $etiqueta, $imagen]) {
        if (!isset($categorias[$categoria])) {
            $orden = count($categorias) + 1;
            $pdo->prepare('INSERT OR IGNORE INTO categorias (nombre, orden) VALUES (?, ?)')->execute([$categoria, $orden]);
            $categorias[$categoria] = (int) $pdo->query("SELECT id FROM categorias WHERE nombre = " . $pdo->quote($categoria))->fetchColumn();
        }
        $st = $pdo->prepare(
            'INSERT INTO productos (categoria_id, nombre, descripcion, precio, imagen, etiqueta, disponible, orden)
             VALUES (?, ?, ?, ?, ?, ?, 1, ?)'
        );
        $st->execute([$categorias[$categoria], $nombre, $descripcion, $precio, $imagen, $etiqueta, ((int) $pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn() + 1) * 10]);
    }
    echo "Menú inicial cargado (" . count(config()['productos_iniciales']) . " productos de ejemplo).\n";
}

// Mesas iniciales.
if ((int) $pdo->query('SELECT COUNT(*) FROM mesas')->fetchColumn() === 0) {
    mesas_reemplazar(implode("\n", config()['mesas_iniciales']));
    echo "Mesas iniciales cargadas.\n";
}

// Contraseña del panel.
$clave = $opciones['clave'] ?? null;
if ($clave !== null || ajuste('admin_hash') === '') {
    if ($clave === null || $clave === false || $clave === '') {
        $clave = bin2hex(random_bytes(6));
    }
    if (strlen((string) $clave) < 6) {
        fwrite(STDERR, "La contraseña debe tener al menos 6 caracteres.\n");
        exit(1);
    }
    ajustes_guardar(['admin_hash' => password_hash((string) $clave, PASSWORD_DEFAULT)]);
    echo "\nContraseña del panel: {$clave}\n";
    echo "Guárdala. Para cambiarla vuelve a ejecutar: php bin/setup.php --clave=nueva\n";
}

echo "\nListo. Base de datos: " . config()['db_path'] . "\n";
echo "Inicia el sitio con: bin/iniciar.sh\n";
