<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

if (admin_autenticado()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $error = 'Usuario o contraseña incorrectos.';
    } else {
        $usuario = trim((string) ($_POST['usuario'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $admin = intentar_login($usuario, $password);

        if ($admin !== null) {
            login_admin($admin);
            header('Location: index.php');
            exit;
        }

        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ingresar — Admin Tienda de Pesca Santa Clara</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="pagina-login">
<main class="tarjeta-login">
    <h1>Tienda de Pesca Santa Clara</h1>
    <p class="subtitulo">Panel de administración</p>

    <?php if ($error !== ''): ?>
        <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" novalidate>
        <?= csrf_campo() ?>

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" required autofocus autocomplete="username">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <button type="submit" class="boton boton-primario">Ingresar</button>
    </form>
</main>
</body>
</html>
