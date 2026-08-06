<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: subcategorias.php');
    exit;
}

if (!csrf_validar($_POST['csrf_token'] ?? null)) {
    flash_set('error', 'La sesión expiró o la solicitud no es válida. Intenta de nuevo.');
    header('Location: subcategorias.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    flash_set('error', 'Subcategoría no válida.');
    header('Location: subcategorias.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT id FROM subcategorias WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$subcategoria = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$subcategoria) {
    flash_set('error', 'La subcategoría solicitada no existe.');
    header('Location: subcategorias.php');
    exit;
}

$eliminar = $mysqli->prepare('DELETE FROM subcategorias WHERE id = ?');
$eliminar->bind_param('i', $id);
$eliminar->execute();
$eliminar->close();

flash_set('exito', 'Subcategoría eliminada ✓. Los productos que la tenían quedaron sin subcategoría.');
header('Location: subcategorias.php');
exit;
