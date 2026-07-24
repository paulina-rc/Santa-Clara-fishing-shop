<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

requerir_login();

function producto_toggle_destino(): string
{
    $destino = 'productos.php';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    if ($referer !== '' && str_starts_with($referer, BASE_URL . '/admin/')) {
        $destino = $referer;
    }

    return $destino;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: productos.php');
    exit;
}

if (!csrf_validar($_POST['csrf_token'] ?? null)) {
    flash_set('error', 'La sesión expiró o la solicitud no es válida. Intenta de nuevo.');
    header('Location: ' . producto_toggle_destino());
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$accion = (string) ($_POST['accion'] ?? '');

if (!$id || !in_array($accion, ['activo', 'destacado'], true)) {
    flash_set('error', 'Solicitud no válida.');
    header('Location: ' . producto_toggle_destino());
    exit;
}

$campo = $accion === 'activo' ? 'activo' : 'destacado';

$stmt = $mysqli->prepare("UPDATE productos SET {$campo} = NOT {$campo} WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$filasAfectadas = $stmt->affected_rows;
$stmt->close();

if ($filasAfectadas === 0) {
    flash_set('error', 'El producto solicitado no existe.');
} else {
    flash_set('exito', 'Producto actualizado ✓');
}

header('Location: ' . producto_toggle_destino());
exit;
