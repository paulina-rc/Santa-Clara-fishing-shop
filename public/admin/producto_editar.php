<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/productos_helpers.php';

requerir_login();

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

$errores = [];

$nombre = $producto['nombre'];
$marca = $producto['marca'] ?? '';
$categoriaId = (int) $producto['categoria_id'];
$descripcion = $producto['descripcion'] ?? '';
$precio = (string) $producto['precio'];
$destacado = (bool) $producto['destacado'];
$activo = (bool) $producto['activo'];
$imagenActual = $producto['imagen'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar($_POST['csrf_token'] ?? null)) {
        $errores[] = 'La sesión expiró o la solicitud no es válida. Intenta de nuevo.';
    }

    $nombre = trim((string) ($_POST['nombre'] ?? ''));
    $marca = trim((string) ($_POST['marca'] ?? ''));
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
    $precioTexto = trim((string) ($_POST['precio'] ?? ''));
    $destacado = isset($_POST['destacado']);
    $activo = isset($_POST['activo']);
    $borrarImagen = isset($_POST['borrar_imagen']);
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

    if (!is_numeric($precioTexto) || (float) $precioTexto < 0) {
        $errores[] = 'El precio debe ser un número mayor o igual a 0.';
    }

    $imagenValidada = false;
    if (!empty($_FILES['imagen']['name'])) {
        $resultado = validar_imagen($_FILES['imagen']);
        if (!$resultado['ok']) {
            $errores[] = $resultado['error'];
        } else {
            $imagenValidada = true;
        }
    }

    if ($errores === []) {
        $precioFinal = (float) $precioTexto;
        $marcaFinal = $marca !== '' ? $marca : null;
        $descripcionFinal = $descripcion !== '' ? $descripcion : null;
        $destacadoInt = $destacado ? 1 : 0;
        $activoInt = $activo ? 1 : 0;

        $nombreImagenFinal = $imagenActual;

        if ($imagenValidada) {
            $nuevoArchivo = guardar_imagen($_FILES['imagen'], $id, $nombre);
            if ($nuevoArchivo !== false) {
                borrar_imagen($imagenActual);
                $nombreImagenFinal = $nuevoArchivo;
            } else {
                $errores[] = 'No se pudo guardar la nueva imagen. El producto no se actualizó.';
            }
        } elseif ($borrarImagen) {
            borrar_imagen($imagenActual);
            $nombreImagenFinal = null;
        }

        if ($errores === []) {
            $stmt = $mysqli->prepare(
                'UPDATE productos
                 SET categoria_id = ?, nombre = ?, marca = ?, descripcion = ?, precio = ?, imagen = ?, destacado = ?, activo = ?
                 WHERE id = ?'
            );
            $stmt->bind_param(
                'isssdsiii',
                $categoriaId,
                $nombre,
                $marcaFinal,
                $descripcionFinal,
                $precioFinal,
                $nombreImagenFinal,
                $destacadoInt,
                $activoInt,
                $id
            );
            $stmt->execute();
            $stmt->close();

            flash_set('exito', 'Producto actualizado ✓');
            header('Location: productos.php');
            exit;
        }
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
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<header class="cabecera-admin">
    <h1>Tienda de Pesca Santa Clara</h1>
    <nav class="nav-admin">
        <a href="index.php">Inicio</a>
        <a href="productos.php">Productos</a>
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

        <label for="categoria_id">Categoría *</label>
        <select id="categoria_id" name="categoria_id" required>
            <option value="">Selecciona una categoría</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>>
                    <?= e($categoria['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="6"><?= e($descripcion) ?></textarea>

        <label for="precio">Precio (₡) *</label>
        <input type="number" id="precio" name="precio" min="0" step="1" required value="<?= e($precio) ?>">

        <?php $imagenSrc = producto_imagen_src($imagenActual); ?>
        <?php if ($imagenSrc !== null): ?>
            <label>Imagen actual</label>
            <img class="imagen-actual" src="<?= e($imagenSrc) ?>" alt="<?= e($nombre) ?>">
            <label class="campo-checkbox">
                <input type="checkbox" name="borrar_imagen" value="1">
                Eliminar imagen actual
            </label>
        <?php endif; ?>

        <label for="imagen"><?= $imagenSrc !== null ? 'Reemplazar imagen' : 'Imagen' ?></label>
        <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp" onchange="previsualizarImagen(this)">
        <p class="nota-campo">Máximo 3 MB. JPG, PNG o WEBP.</p>
        <img id="preview-imagen" class="preview-imagen" alt="Vista previa" style="display:none;">

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

<script>
function previsualizarImagen(input) {
    const preview = document.getElementById('preview-imagen');
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
</script>
</body>
</html>
