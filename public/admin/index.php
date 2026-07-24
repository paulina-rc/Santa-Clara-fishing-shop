<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

requerir_login();

$stmt = $mysqli->prepare(
    'SELECT COUNT(*) AS total, SUM(activo = 1) AS visibles, SUM(activo = 0) AS ocultos FROM productos'
);
$stmt->execute();
$contadores = $stmt->get_result()->fetch_assoc();
$stmt->close();

$totalProductos = (int) ($contadores['total'] ?? 0);
$productosVisibles = (int) ($contadores['visibles'] ?? 0);
$productosOcultos = (int) ($contadores['ocultos'] ?? 0);
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de administración — Tienda de Pesca Santa Clara</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<header class="cabecera-admin">
    <h1>Tienda de Pesca Santa Clara</h1>
    <nav class="nav-admin">
        <a href="index.php">Inicio</a>
        <a href="productos.php">Productos</a>
        <a href="<?= e(BASE_URL) ?>/" target="_blank" rel="noopener">Ver sitio</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="contenido-admin">
    <?= flash_render() ?>

    <p class="saludo">Hola, <strong><?= e($_SESSION['admin_nombre'] ?? '') ?></strong>.</p>

    <section class="tarjetas-contadores">
        <div class="tarjeta">
            <span class="tarjeta-numero"><?= $totalProductos ?></span>
            <span class="tarjeta-etiqueta">Productos totales</span>
        </div>
        <div class="tarjeta">
            <span class="tarjeta-numero"><?= $productosVisibles ?></span>
            <span class="tarjeta-etiqueta">Visibles</span>
        </div>
        <div class="tarjeta">
            <span class="tarjeta-numero"><?= $productosOcultos ?></span>
            <span class="tarjeta-etiqueta">Ocultos</span>
        </div>
    </section>
</main>
</body>
</html>
