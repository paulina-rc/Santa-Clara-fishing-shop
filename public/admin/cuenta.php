<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

requerir_login();

$logoAdmin = is_file(__DIR__ . '/../assets/img/logo.png') ? '../assets/img/logo.png' : null;

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $errores[] = 'La sesión expiró o la solicitud no es válida. Intenta de nuevo.';
    }

    $actual = (string) ($_POST['actual'] ?? '');
    $nueva = (string) ($_POST['nueva'] ?? '');
    $confirmar = (string) ($_POST['confirmar'] ?? '');

    $stmt = $mysqli->prepare('SELECT password_hash FROM admins WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $_SESSION['admin_id']);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$admin || !password_verify($actual, $admin['password_hash'])) {
        $errores[] = 'La contraseña actual no es correcta.';
    }

    if (mb_strlen($nueva) < 8) {
        $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
    }

    if ($nueva !== $confirmar) {
        $errores[] = 'La confirmación no coincide con la nueva contraseña.';
    }

    if ($errores === []) {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $actualizar = $mysqli->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
        $actualizar->bind_param('si', $hash, $_SESSION['admin_id']);
        $actualizar->execute();
        $actualizar->close();

        unset($_SESSION['admin_id'], $_SESSION['admin_usuario'], $_SESSION['admin_nombre']);
        session_regenerate_id(true);
        flash_set('exito', 'Contraseña actualizada, ingresa de nuevo.');

        header('Location: login.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi cuenta — Panel de administración</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(BASE_URL) ?>/assets/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= e(BASE_URL) ?>/assets/img/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?= e(BASE_URL) ?>/assets/img/apple-touch-icon.png">
<link rel="icon" type="image/x-icon" href="<?= e(BASE_URL) ?>/assets/img/favicon.ico">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<header class="cabecera-admin">
    <div class="cabecera-admin-marca">
        <?php if ($logoAdmin !== null): ?>
            <img src="<?= e($logoAdmin) ?>" alt="" class="cabecera-admin-logo">
        <?php endif; ?>
        <h1>Tienda de Pesca Santa Clara</h1>
    </div>
    <nav class="nav-admin">
        <a href="index.php">Inicio</a>
        <a href="productos.php">Productos</a>
        <a href="cuenta.php">Mi cuenta</a>
        <a href="<?= e(BASE_URL) ?>/" target="_blank" rel="noopener">Ver sitio</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="contenido-admin">
    <h2>Mi cuenta</h2>
    <p class="saludo">Cambiar la contraseña de <strong><?= e($_SESSION['admin_nombre'] ?? '') ?></strong>.</p>

    <?php if ($errores !== []): ?>
        <div class="alerta alerta-error">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="cuenta.php" class="formulario-producto formulario-angosto">
        <?= csrf_campo() ?>

        <label for="actual">Contraseña actual</label>
        <input type="password" id="actual" name="actual" required autocomplete="current-password">

        <label for="nueva">Nueva contraseña</label>
        <input type="password" id="nueva" name="nueva" required minlength="8" autocomplete="new-password">

        <label for="confirmar">Confirmar nueva contraseña</label>
        <input type="password" id="confirmar" name="confirmar" required minlength="8" autocomplete="new-password">

        <div class="acciones-formulario">
            <button type="submit" class="boton boton-primario">Actualizar contraseña</button>
        </div>
    </form>
</main>
</body>
</html>
