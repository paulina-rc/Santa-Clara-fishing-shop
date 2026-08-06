<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';

requerir_login();
header('Content-Type: application/json; charset=utf-8');

$categoriaId = filter_input(INPUT_GET, 'categoria_id', FILTER_VALIDATE_INT);

if (!$categoriaId || $categoriaId <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = $mysqli->prepare('SELECT id, nombre FROM subcategorias WHERE categoria_id = ? AND activa = 1 ORDER BY orden ASC, nombre ASC');
$stmt->bind_param('i', $categoriaId);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($filas);
