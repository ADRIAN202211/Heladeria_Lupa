<?php
declare(strict_types=1);

/** Conexión única a SQLite. Crea la base y las tablas si no existen. */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $ruta = config()['db_path'];
    if (!is_dir(dirname($ruta))) {
        mkdir(dirname($ruta), 0775, true);
    }

    $pdo = new PDO('sqlite:' . $ruta, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    instalar_esquema($pdo);
    return $pdo;
}

function instalar_esquema(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS categorias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT NOT NULL UNIQUE,
        orden INTEGER NOT NULL DEFAULT 0
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS productos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        categoria_id INTEGER REFERENCES categorias(id) ON DELETE SET NULL,
        nombre TEXT NOT NULL,
        descripcion TEXT NOT NULL DEFAULT \'\',
        precio INTEGER NOT NULL,
        imagen TEXT NOT NULL DEFAULT \'\',
        etiqueta TEXT NOT NULL DEFAULT \'\',
        disponible INTEGER NOT NULL DEFAULT 1,
        orden INTEGER NOT NULL DEFAULT 0
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS mesas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT NOT NULL UNIQUE,
        orden INTEGER NOT NULL DEFAULT 0
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS pedidos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        creado_en TEXT NOT NULL,
        nombre TEXT NOT NULL,
        telefono TEXT NOT NULL,
        entrega TEXT NOT NULL,
        mesa TEXT NOT NULL DEFAULT \'\',
        direccion TEXT NOT NULL DEFAULT \'\',
        barrio TEXT NOT NULL DEFAULT \'\',
        pago TEXT NOT NULL,
        nota TEXT NOT NULL DEFAULT \'\',
        items TEXT NOT NULL,
        subtotal INTEGER NOT NULL,
        domicilio INTEGER NOT NULL DEFAULT 0,
        total INTEGER NOT NULL,
        estado TEXT NOT NULL DEFAULT \'pendiente\'
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS ajustes (
        clave TEXT PRIMARY KEY,
        valor TEXT NOT NULL DEFAULT \'\'
    )');
}

/** Lee un ajuste del negocio; si no está guardado usa el valor por defecto de config. */
function ajuste(string $clave): string
{
    $st = db()->prepare('SELECT valor FROM ajustes WHERE clave = ?');
    $st->execute([$clave]);
    $valor = $st->fetchColumn();
    if ($valor === false) {
        return (string) (config()['ajustes'][$clave] ?? '');
    }
    return (string) $valor;
}

/** Guarda varios ajustes a la vez. */
function ajustes_guardar(array $valores): void
{
    $st = db()->prepare(
        'INSERT INTO ajustes (clave, valor) VALUES (?, ?)
         ON CONFLICT(clave) DO UPDATE SET valor = excluded.valor'
    );
    foreach ($valores as $clave => $valor) {
        $st->execute([$clave, (string) $valor]);
    }
}
