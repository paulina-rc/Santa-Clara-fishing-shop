<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/productos_helpers.php';

requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: productos.php');
    exit;
}

if (!csrf_validar($_POST['csrf_token'] ?? null)) {
    flash_set('error', 'La sesión expiró o la solicitud no es válida. Intenta de nuevo.');
    header('Location: productos.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    flash_set('error', 'Producto no válido.');
    header('Location: productos.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT imagen FROM productos WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    flash_set('error', 'El producto solicitado no existe.');
    header('Location: productos.php');
    exit;
}

$eliminar = $mysqli->prepare('DELETE FROM productos WHERE id = ?');
$eliminar->bind_param('i', $id);
$eliminar->execute();
$eliminar->close();

borrar_imagen($producto['imagen']);

flash_set('exito', 'Producto eliminado ✓');
header('Location: productos.php');
exit;
