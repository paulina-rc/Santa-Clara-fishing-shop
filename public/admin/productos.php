<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

requerir_login();

$logoAdmin = is_file(__DIR__ . '/../assets/img/logo.png') ? '../assets/img/logo.png' : null;

/**
 * Reconstruye la URL del listado conservando los filtros actuales,
 * permitiendo sobreescribir (o quitar, con null) parametros puntuales.
 */
function productos_url(array $overrides = []): string
{
    $parametros = array_merge($_GET, $overrides);

    foreach ($parametros as $clave => $valor) {
        if ($valor === null || $valor === '') {
            unset($parametros[$clave]);
        }
    }

    $query = http_build_query($parametros);

    return 'productos.php' . ($query !== '' ? '?' . $query : '');
}

$busqueda = trim((string) ($_GET['q'] ?? ''));
$categoriaId = (int) (filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: 0);
$subcategoriaId = (int) (filter_input(INPUT_GET, 'subcategoria', FILTER_VALIDATE_INT) ?: 0);

$estado = (string) ($_GET['estado'] ?? 'todos');
if (!in_array($estado, ['todos', 'activos', 'ocultos'], true)) {
    $estado = 'todos';
}

$porPagina = 20;
$pagina = (int) (filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?: 1);
if ($pagina < 1) {
    $pagina = 1;
}

$condiciones = [];
$tipos = '';
$parametros = [];

if ($busqueda !== '') {
    $condiciones[] = 'p.nombre LIKE ?';
    $tipos .= 's';
    $parametros[] = '%' . $busqueda . '%';
}

if ($categoriaId > 0) {
    $condiciones[] = 'p.categoria_id = ?';
    $tipos .= 'i';
    $parametros[] = $categoriaId;
}

if ($subcategoriaId > 0) {
    $condiciones[] = 'p.subcategoria_id = ?';
    $tipos .= 'i';
    $parametros[] = $subcategoriaId;
}

if ($estado === 'activos') {
    $condiciones[] = 'p.activo = 1';
} elseif ($estado === 'ocultos') {
    $condiciones[] = 'p.activo = 0';
}

$whereSql = $condiciones !== [] ? ' AND ' . implode(' AND ', $condiciones) : '';

$stmtConteo = $mysqli->prepare(
    'SELECT COUNT(*) AS total FROM productos p JOIN categorias c ON p.categoria_id = c.id WHERE 1=1' . $whereSql
);
if ($tipos !== '') {
    $stmtConteo->bind_param($tipos, ...$parametros);
}
$stmtConteo->execute();
$totalProductos = (int) $stmtConteo->get_result()->fetch_assoc()['total'];
$stmtConteo->close();

$totalPaginas = (int) max(1, ceil($totalProductos / $porPagina));
if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}
$offset = ($pagina - 1) * $porPagina;

$stmt = $mysqli->prepare(
    'SELECT p.*, c.nombre AS cat_nombre, c.slug AS cat_slug,
            s.nombre AS sub_nombre, s.slug AS sub_slug
     FROM productos p
     JOIN categorias c ON p.categoria_id = c.id
     LEFT JOIN subcategorias s ON p.subcategoria_id = s.id
     WHERE 1=1' . $whereSql . '
     ORDER BY p.actualizado_en DESC
     LIMIT ? OFFSET ?'
);
$tiposFinal = $tipos . 'ii';
$parametrosFinal = [...$parametros, $porPagina, $offset];
$stmt->bind_param($tiposFinal, ...$parametrosFinal);
$stmt->execute();
$productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$categorias = $mysqli->query('SELECT id, nombre FROM categorias ORDER BY orden')->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos — Panel de administración</title>
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
        <h2>Productos</h2>
        <a href="producto_nuevo.php" class="boton boton-dorado">+ Agregar producto</a>
    </div>

    <form method="get" action="productos.php" class="filtros-productos">
        <div class="campo-filtro">
            <label for="q">Buscar</label>
            <input type="text" id="q" name="q" value="<?= e($busqueda) ?>" placeholder="Nombre del producto">
        </div>

        <div class="campo-filtro">
            <label for="categoria">Categoría</label>
            <select id="categoria" name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>>
                        <?= e($categoria['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo-filtro">
            <label for="subcategoria">Subcategoría</label>
            <select id="subcategoria" name="subcategoria" data-actual="<?= $subcategoriaId ?>">
                <option value="">Todas</option>
            </select>
        </div>

        <div class="campo-filtro">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="todos" <?= $estado === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="activos" <?= $estado === 'activos' ? 'selected' : '' ?>>Activos</option>
                <option value="ocultos" <?= $estado === 'ocultos' ? 'selected' : '' ?>>Ocultos</option>
            </select>
        </div>

        <div class="campo-filtro campo-filtro-acciones">
            <button type="submit" class="boton boton-primario">Aplicar</button>
            <a href="productos.php">Limpiar</a>
        </div>
    </form>

    <?php if ($productos === []): ?>
        <p class="sin-resultados">No hay productos que coincidan con los filtros.</p>
    <?php else: ?>
        <div class="tabla-productos-contenedor">
            <table class="tabla-productos">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <?php $imagenSrc = producto_imagen_src($producto['imagen']); ?>
                        <tr>
                            <td>
                                <?php if ($imagenSrc !== null): ?>
                                    <img class="miniatura-producto" src="<?= e($imagenSrc) ?>" alt="<?= e($producto['nombre']) ?>">
                                <?php else: ?>
                                    <div class="miniatura-producto miniatura-placeholder" aria-hidden="true">Sin foto</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="celda-nombre"><?= e($producto['nombre']) ?></div>
                                <?php if (!empty($producto['marca'])): ?>
                                    <div class="celda-marca"><?= e($producto['marca']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-categoria">
                                    <?= e($producto['cat_nombre']) ?><?php if (!empty($producto['sub_nombre'])): ?> · <?= e($producto['sub_nombre']) ?><?php endif; ?>
                                </span>
                            </td>
                            <td><?= precio((float) $producto['precio']) ?></td>
                            <td>
                                <span class="badge <?= $producto['activo'] ? 'badge-activo' : 'badge-oculto' ?>">
                                    <?= $producto['activo'] ? 'Activo' : 'Oculto' ?>
                                </span>
                                <?php if ($producto['destacado']): ?>
                                    <span class="badge badge-destacado">Destacado</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acciones-fila">
                                    <a class="boton-mini" href="producto_editar.php?id=<?= (int) $producto['id'] ?>">Editar</a>

                                    <form method="post" action="producto_toggle.php" class="form-inline">
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
                                        <input type="hidden" name="accion" value="activo">
                                        <button type="submit" class="boton-mini">
                                            <?= $producto['activo'] ? 'Ocultar' : 'Activar' ?>
                                        </button>
                                    </form>

                                    <form method="post" action="producto_toggle.php" class="form-inline">
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
                                        <input type="hidden" name="accion" value="destacado">
                                        <button type="submit" class="boton-mini">
                                            <?= $producto['destacado'] ? 'Quitar destacado' : 'Destacar' ?>
                                        </button>
                                    </form>

                                    <form method="post" action="producto_eliminar.php" class="form-inline" onsubmit="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.');">
                                        <?= csrf_campo() ?>
                                        <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
                                        <button type="submit" class="boton-mini boton-mini-peligro">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPaginas > 1): ?>
            <nav class="paginacion">
                <?php if ($pagina > 1): ?>
                    <a href="<?= e(productos_url(['pagina' => $pagina - 1])) ?>">&laquo; Anterior</a>
                <?php endif; ?>

                <span class="paginacion-info">Página <?= $pagina ?> de <?= $totalPaginas ?></span>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="<?= e(productos_url(['pagina' => $pagina + 1])) ?>">Siguiente &raquo;</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</main>
<script src="../assets/js/admin.js"></script>
</body>
</html>
