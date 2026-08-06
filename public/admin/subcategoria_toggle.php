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
    flash_set('error', 'Solicitud no válida.');
    header('Location: subcategorias.php');
    exit;
}

$stmt = $mysqli->prepare('UPDATE subcategorias SET activa = NOT activa WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$filasAfectadas = $stmt->affected_rows;
$stmt->close();

if ($filasAfectadas === 0) {
    flash_set('error', 'La subcategoría solicitada no existe.');
} else {
    flash_set('exito', 'Subcategoría actualizada ✓');
}

header('Location: subcategorias.php');
exit;
