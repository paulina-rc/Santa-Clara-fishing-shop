<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

requerir_login();

$logoAdmin = is_file(__DIR__ . '/../assets/img/logo.png') ? '../assets/img/logo.png' : null;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    flash_set('error', 'Subcategoría no válida.');
    header('Location: subcategorias.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT * FROM subcategorias WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$subcategoria = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$subcategoria) {
    flash_set('error', 'La subcategoría solicitada no existe.');
    header('Location: subcategorias.php');
    exit;
}

$categorias = $mysqli->query('SELECT id, nombre FROM categorias ORDER BY orden')->fetch_all(MYSQLI_ASSOC);

$errores = [];

$nombre = $subcategoria['nombre'];
$categoriaId = (int) $subcategoria['categoria_id'];
$orden = (int) $subcategoria['orden'];
$activa = (bool) $subcategoria['activa'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $errores[] = 'La sesión expiró o la solicitud no es válida. Intenta de nuevo.';
    }

    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $orden = (int) ($_POST['orden'] ?? 0);
    $activa = isset($_POST['activa']);

    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre es obligatorio y debe tener máximo 100 caracteres.';
    }

    $categoriaValida = false;
    foreach ($categorias as $categoria) {
        if ((int) $categoria['id'] === $categoriaId) {
            $categoriaValida = true;
            break;
        }
    }
    if (!$categoriaValida) {
        $errores[] = 'Selecciona una categoría válida.';
    }

    $slug = slugify($nombre);

    if ($errores === [] && $slug === '') {
        $errores[] = 'El nombre no genera un slug válido. Probá con otro texto.';
    }

    if ($errores === []) {
        $stmtDup = $mysqli->prepare('SELECT id FROM subcategorias WHERE categoria_id = ? AND slug = ? AND id <> ? LIMIT 1');
        $stmtDup->bind_param('isi', $categoriaId, $slug, $id);
        $stmtDup->execute();
        $duplicado = $stmtDup->get_result()->fetch_assoc();
        $stmtDup->close();

        if ($duplicado) {
            $errores[] = 'Ya existe una subcategoría con ese nombre en esta categoría.';
        }
    }

    if ($errores === []) {
        $activaInt = $activa ? 1 : 0;
        $stmt = $mysqli->prepare(
            'UPDATE subcategorias
             SET categoria_id = ?, slug = ?, nombre = ?, orden = ?, activa = ?
             WHERE id = ?'
        );
        $stmt->bind_param('issiii', $categoriaId, $slug, $nombre, $orden, $activaInt, $id);
        $stmt->execute();
        $stmt->close();

        flash_set('exito', 'Subcategoría actualizada ✓');
        header('Location: subcategorias.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar subcategoría — Panel de administración</title>
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
        <a href="subcategorias.php">Subcategorías</a>
        <a href="cuenta.php">Mi cuenta</a>
        <a href="<?= e(BASE_URL) ?>/" target="_blank" rel="noopener">Ver sitio</a>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
</header>

<main class="contenido-admin">
    <h2>Editar subcategoría</h2>

    <?php if ($errores !== []): ?>
        <div class="alerta alerta-error">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="subcategoria_editar.php?id=<?= (int) $id ?>" class="formulario-producto formulario-angosto">
        <?= csrf_campo() ?>

        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" required value="<?= e($nombre) ?>">

        <label for="categoria_id">Categoría padre *</label>
        <select id="categoria_id" name="categoria_id" required>
            <option value="">Selecciona una categoría</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>>
                    <?= e($categoria['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="orden">Orden</label>
        <input type="number" id="orden" name="orden" min="0" step="1" value="<?= (int) $orden ?>">
        <p class="nota-campo">Controla el orden en los chips del catálogo. Menor número aparece primero.</p>

        <label class="campo-checkbox">
            <input type="checkbox" name="activa" <?= $activa ? 'checked' : '' ?>>
            Visible en el sitio público
        </label>

        <div class="acciones-formulario">
            <button type="submit" class="boton boton-primario">Guardar cambios</button>
            <a href="subcategorias.php" class="boton boton-secundario">Cancelar</a>
        </div>
    </form>
</main>
</body>
</html>
