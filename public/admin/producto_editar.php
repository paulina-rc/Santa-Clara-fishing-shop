<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/productos_helpers.php';

requerir_login();

$logoAdmin = is_file(__DIR__ . '/../assets/img/logo.png') ? '../assets/img/logo.png' : null;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    flash_set('error', 'Producto no válido.');
    header('Location: productos.php');
    exit;
}

$stmt = $mysqli->prepare('SELECT * FROM productos WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    flash_set('error', 'El producto solicitado no existe.');
    header('Location: productos.php');
    exit;
}

$categorias = $mysqli->query('SELECT id, nombre FROM categorias WHERE activa = 1 ORDER BY orden')->fetch_all(MYSQLI_ASSOC);
$imagenes = imagenes_producto($id);

$errores = [];

$nombre = $producto['nombre'];
$marca = $producto['marca'] ?? '';
$categoriaId = (int) $producto['categoria_id'];
$subcategoriaId = (int) ($producto['subcategoria_id'] ?? 0);
$descripcion = $producto['descripcion'] ?? '';
$precio = (string) $producto['precio'];
$destacado = (bool) $producto['destacado'];
$activo = (bool) $producto['activo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $errores[] = 'La sesión expiró o la solicitud no es válida. Intenta de nuevo.';
    }

    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $marca = trim((string) ($_POST['marca'] ?? ''));
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $subcategoriaId = (int) ($_POST['subcategoria_id'] ?? 0);
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $precioTexto = trim((string) ($_POST['precio'] ?? ''));
    $destacado = isset($_POST['destacado']);
    $activo = isset($_POST['activo']);
    $precio = $precioTexto;

    if ($nombre === '' || mb_strlen($nombre) > 150) {
        $errores[] = 'El nombre es obligatorio y debe tener máximo 150 caracteres.';
    }

    if (mb_strlen($marca) > 80) {
        $errores[] = 'La marca debe tener máximo 80 caracteres.';
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

    if ($subcategoriaId > 0) {
        $stmtSub = $mysqli->prepare('SELECT id FROM subcategorias WHERE id = ? AND categoria_id = ? LIMIT 1');
        $stmtSub->bind_param('ii', $subcategoriaId, $categoriaId);
        $stmtSub->execute();
        $subcategoriaValida = $stmtSub->get_result()->fetch_assoc();
        $stmtSub->close();

        if (!$subcategoriaValida) {
            $errores[] = 'La subcategoría elegida no corresponde a la categoría seleccionada.';
        }
    }

    if (!is_numeric($precioTexto) || (float) $precioTexto < 0) {
        $errores[] = 'El precio debe ser un número mayor o igual a 0.';
    }

    if ($errores === []) {
        $precioFinal = (float) $precioTexto;
        $marcaFinal = $marca !== '' ? $marca : null;
        $descripcionFinal = $descripcion !== '' ? $descripcion : null;
        $destacadoInt = $destacado ? 1 : 0;
        $activoInt = $activo ? 1 : 0;
        $subcategoriaFinal = $subcategoriaId > 0 ? $subcategoriaId : null;

        $stmt = $mysqli->prepare(
            'UPDATE productos
             SET categoria_id = ?, subcategoria_id = ?, nombre = ?, marca = ?, descripcion = ?, precio = ?, destacado = ?, activo = ?
             WHERE id = ?'
        );
        $stmt->bind_param(
            'iisssdii',
            $categoriaId,
            $subcategoriaFinal,
            $nombre,
            $marcaFinal,
            $descripcionFinal,
            $precioFinal,
            $destacadoInt,
            $activoInt,
            $id
        );
        $stmt->execute();
        $stmt->close();

        $archivosImagenes = $_FILES['imagenes'] ?? ['name' => [], 'type' => [], 'tmp_name' => [], 'error' => [], 'size' => []];
        $resultadoImagenes = guardar_imagenes_multiples($archivosImagenes, $id, $nombre);
        foreach ($resultadoImagenes['errores'] as $errorImagen) {
            flash_set('error', $errorImagen);
        }

        flash_set('exito', 'Producto actualizado ✓');
        header('Location: productos.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar producto — Panel de administración</title>
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
    <h2>Editar producto</h2>

    <?php if ($errores !== []): ?>
        <div class="alerta alerta-error">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="producto_editar.php?id=<?= (int) $id ?>" enctype="multipart/form-data" class="formulario-producto">
        <?= csrf_campo() ?>

        <label for="nombre">Nombre *</label>
        <input type="text" id="nombre" name="nombre" maxlength="150" required value="<?= e($nombre) ?>">

        <label for="marca">Marca</label>
        <input type="text" id="marca" name="marca" maxlength="80" value="<?= e($marca) ?>">

        <label for="categoria-select">Categoría *</label>
        <select id="categoria-select" name="categoria_id" required>
            <option value="">Selecciona una categoría</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>>
                    <?= e($categoria['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="subcategoria-select">Subcategoría (opcional)</label>
        <select id="subcategoria-select" name="subcategoria_id" data-actual="<?= $subcategoriaId ?>">
            <option value="">— Sin subcategoría —</option>
        </select>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="6"><?= e($descripcion) ?></textarea>

        <label for="precio">Precio (₡) *</label>
        <input type="number" id="precio" name="precio" min="0" step="1" required value="<?= e($precio) ?>">

        <div class="fotos-actuales">
            <h3>Fotos actuales (<?= count($imagenes) ?>/10)</h3>

            <?php if (count($imagenes) === 0): ?>
                <p class="hint-foto">Este producto todavía no tiene fotos.</p>
            <?php else: ?>
                <div class="fotos-grid">
                    <?php foreach ($imagenes as $img): ?>
                        <?php $fotoSrc = producto_imagen_src($img['archivo']); ?>
                        <div class="foto-item <?= $img['es_principal'] ? 'es-principal' : '' ?>">
                            <?php if ($img['es_principal']): ?>
                                <span class="badge-principal">Principal</span>
                            <?php endif; ?>
                            <?php if ($fotoSrc !== null): ?>
                                <img src="<?= e($fotoSrc) ?>" alt="Foto de <?= e($nombre) ?>">
                            <?php endif; ?>

                            <div class="foto-acciones">
                                <?php if (!$img['es_principal']): ?>
                                    <form method="post" action="foto_marcar_principal.php" class="form-inline">
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="imagen_id" value="<?= (int) $img['id'] ?>">
                                        <input type="hidden" name="producto_id" value="<?= (int) $id ?>">
                                        <button type="submit" class="btn-mini">Hacer principal</button>
                                    </form>
                                <?php endif; ?>

                                <form method="post" action="foto_eliminar.php" class="form-inline" onsubmit="return confirm('¿Borrar esta foto?');">
                                    <?= csrf_campo() ?>
                                    <input type="hidden" name="imagen_id" value="<?= (int) $img['id'] ?>">
                                    <input type="hidden" name="producto_id" value="<?= (int) $id ?>">
                                    <button type="submit" class="btn-mini btn-peligro">Borrar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (count($imagenes) < 10): ?>
            <label for="imagenes-input">Agregar más fotos (<?= 10 - count($imagenes) ?> restantes)</label>
            <input type="file" id="imagenes-input" name="imagenes[]" multiple accept="image/jpeg,image/png,image/webp" data-sin-fotos="<?= count($imagenes) === 0 ? '1' : '0' ?>">
            <p class="nota-campo">Máximo 3 MB por foto. JPG, PNG o WEBP.</p>
            <div id="preview-imagenes" class="preview-grid"></div>
        <?php else: ?>
            <p class="hint-foto">Este producto ya tiene el máximo de 10 fotos. Borra alguna para poder agregar más.</p>
        <?php endif; ?>

        <label class="campo-checkbox">
            <input type="checkbox" name="destacado" <?= $destacado ? 'checked' : '' ?>>
            Mostrar en la sección "Más vendidos" de la home
        </label>

        <label class="campo-checkbox">
            <input type="checkbox" name="activo" <?= $activo ? 'checked' : '' ?>>
            Visible en el sitio público
        </label>

        <div class="acciones-formulario">
            <button type="submit" class="boton boton-primario">Guardar cambios</button>
            <a href="productos.php" class="boton boton-secundario">Cancelar</a>
        </div>
    </form>
</main>

<script src="../assets/js/admin.js"></script>
</body>
</html>
