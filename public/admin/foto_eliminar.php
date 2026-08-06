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

$imagenId = filter_input(INPUT_POST, 'imagen_id', FILTER_VALIDATE_INT);
$productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
$destino = $productoId ? 'producto_editar.php?id=' . $productoId : 'productos.php';

if (!$imagenId || !borrar_imagen_producto($imagenId)) {
    flash_set('error', 'No se pudo borrar la foto.');
} else {
    flash_set('exito', 'Foto eliminada ✓');
}

header('Location: ' . $destino);
exit;
