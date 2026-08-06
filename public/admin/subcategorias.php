<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

requerir_login();

$logoAdmin = is_file(__DIR__ . '/../assets/img/logo.png') ? '../assets/img/logo.png' : null;

$categoriaFiltro = (int) (filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: 0);

$categorias = $mysqli->query('SELECT id, nombre FROM categorias ORDER BY orden')->fetch_all(MYSQLI_ASSOC);

$sql = 'SELECT s.*, c.nombre AS cat_nombre
        FROM subcategorias s
        JOIN categorias c ON s.categoria_id = c.id';
$tipos = '';
$parametros = [];

if ($categoriaFiltro > 0) {
    $sql .= ' WHERE s.categoria_id = ?';
    $tipos = 'i';
    $parametros[] = $categoriaFiltro;
}

$sql .= ' ORDER BY c.orden, s.orden, s.nombre';

$stmt = $mysqli->prepare($sql);
if ($tipos !== '') {
    $stmt->bind_param($tipos, ...$parametros);
}
$stmt->execute();
$subcategorias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$porCategoria = [];
foreach ($subcategorias as $sub) {
    $porCategoria[(int) $sub['categoria_id']][] = $sub;
}

$categoriasAMostrar = $categoriaFiltro > 0
    ? array_filter($categorias, static fn(array $c): bool => (int) $c['id'] === $categoriaFiltro)
    : $categorias;
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Subcategorías — Panel de administración</title>
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

<main class="contenido-admin contenido-admin-ancho">
    <?= flash_render() ?>

    <div class="encabezado-seccion">
        <h2>Subcategorías</h2>
        <a href="subcategoria_nueva.php" class="boton boton-dorado">+ Nueva subcategoría</a>
    </div>

    <form method="get" action="subcategorias.php" class="filtros-productos">
        <div class="campo-filtro">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaFiltro === (int) $categoria['id'] ? 'selected' : '' ?>>
                        <?= e($categoria['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-filtro campo-filtro-acciones">
            <button type="submit" class="boton boton-primario">Aplicar</button>
            <a href="subcategorias.php">Limpiar</a>
        </div>
    </form>

    <?php foreach ($categoriasAMostrar as $categoria): ?>
        <?php $items = $porCategoria[(int) $categoria['id']] ?? []; ?>
        <div class="encabezado-seccion">
            <h3><?= e($categoria['nombre']) ?></h3>
        </div>

        <?php if ($items === []): ?>
            <p class="sin-resultados">Esta categoría no tiene subcategorías.</p>
        <?php else: ?>
            <div class="tabla-productos-contenedor">
                <table class="tabla-productos">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $sub): ?>
                            <tr>
                                <td><div class="celda-nombre"><?= e($sub['nombre']) ?></div></td>
                                <td><?= (int) $sub['orden'] ?></td>
                                <td>
                                    <span class="badge <?= $sub['activa'] ? 'badge-activo' : 'badge-oculto' ?>">
                                        <?= $sub['activa'] ? 'Activa' : 'Inactiva' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="acciones-fila">
                                        <a class="boton-mini" href="subcategoria_editar.php?id=<?= (int) $sub['id'] ?>">Editar</a>

                                        <form method="post" action="subcategoria_toggle.php" class="form-inline">
                                            <?= csrf_campo() ?>
                                            <input type="hidden" name="id" value="<?= (int) $sub['id'] ?>">
                                            <button type="submit" class="boton-mini">
                                                <?= $sub['activa'] ? 'Desactivar' : 'Activar' ?>
                                            </button>
                                        </form>

                                        <form method="post" action="subcategoria_eliminar.php" class="form-inline" onsubmit="return confirm('¿Eliminar esta subcategoría? Los productos que la tengan quedarán sin subcategoría. Esta acción no se puede deshacer.');">
                                            <?= csrf_campo() ?>
                                            <input type="hidden" name="id" value="<?= (int) $sub['id'] ?>">
                                            <button type="submit" class="boton-mini boton-mini-peligro">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</main>
</body>
</html>
