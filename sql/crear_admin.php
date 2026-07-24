<?php
declare(strict_types=1);

/**
 * Script de un solo uso para crear el primer usuario administrador.
 *
 * Uso:
 *   1. Edita las variables $usuario, $password y $nombre debajo.
 *   2. Ejecuta desde la terminal del servidor: php sql/crear_admin.php
 *   3. BORRA ESTE ARCHIVO del servidor inmediatamente despues de usarlo.
 */

$usuario  = 'admin';
$password = '12345678';
$nombre   = 'Paulina';

if ($password === 'CAMBIAR_ESTA_CONTRASENA') {
    fwrite(STDERR, "Edita este archivo y cambia la variable \$password antes de ejecutarlo.\n");
    exit(1);
}

if (strlen($password) < 8) {
    fwrite(STDERR, "La contraseña debe tener al menos 8 caracteres.\n");
    exit(1);
}

require_once __DIR__ . '/../config/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset(DB_CHARSET);

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare('INSERT INTO admins (usuario, password_hash, nombre) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $usuario, $hash, $nombre);
    $stmt->execute();
    $stmt->close();

    echo "Administrador '{$usuario}' creado correctamente.\n";
    echo "IMPORTANTE: borra este archivo (sql/crear_admin.php) del servidor ahora mismo.\n";
} catch (mysqli_sql_exception $e) {
    fwrite(STDERR, 'Error al crear el administrador: ' . $e->getMessage() . "\n");
    exit(1);
}
