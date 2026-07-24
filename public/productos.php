<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$catSlug = isset($_GET['cat']) ? trim((string) $_GET['cat']) : '';
$categoriaActual = null;
$categoriaInvalida = false;

if ($catSlug !== '') {
    $stmtCat = $mysqli->prepare('SELECT id, slug, nombre FROM categorias WHERE slug = ? AND activa = 1 LIMIT 1');
    $stmtCat->bind_param('s', $catSlug);
    $stmtCat->execute();
    $categoriaActual = $stmtCat->get_result()->fetch_assoc();
    $stmtCat->close();

    if (!$categoriaActual) {
        $categoriaInvalida = true;
    }
}

$productos = [];

if (!$categoriaInvalida) {
    $sql = 'SELECT p.*, c.slug AS cat_slug, c.nombre AS cat_nombre
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.activo = 1';

    if ($categoriaActual) {
        $sql .= ' AND c.slug = ?';
    }

    $sql .= ' ORDER BY p.creado_en DESC';

    $stmt = $mysqli->prepare($sql);
    if ($categoriaActual) {
        $stmt->bind_param('s', $categoriaActual['slug']);
    }
    $stmt->execute();
    $productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$tituloListado = $categoriaActual['nombre'] ?? 'Todos los productos';
$pagina_activa = $categoriaActual['slug'] ?? '';

if ($categoriaInvalida) {
    $meta_titulo = 'Categoría no encontrada · Tienda de Pesca Santa Clara';
    $meta_descripcion = 'Esa categoría no existe. Mirá el catálogo completo de Tienda de Pesca Santa Clara.';
} else {
    $meta_titulo = $tituloListado . ' · Tienda de Pesca Santa Clara';
    $meta_descripcion = $categoriaActual
        ? 'Catálogo de ' . $categoriaActual['nombre'] . ' en Tienda de Pesca Santa Clara. Consultá disponibilidad y precios por WhatsApp.'
        : 'Catálogo completo de equipo de pesca en Tienda de Pesca Santa Clara: cañas, carretes, señuelos, ropa y accesorios.';
}

require __DIR__ . '/includes/header.php';
?>

<section class="seccion-listado">
    <div class="contenedor">
        <?php if ($categoriaInvalida): ?>
            <div class="estado-vacio">
                <p>No encontramos esa categoría.</p>
                <p><a class="boton boton-ghost" href="index.php">Volver al inicio</a></p>
            </div>
        <?php else: ?>
            <div class="listado-encabezado">
                <h1><?= e($tituloListado) ?></h1>
                <span class="seccion-tag mono"><?= count($productos) ?> producto<?= count($productos) === 1 ? '' : 's' ?></span>
            </div>

            <div class="chips-filtro">
                <a class="chip <?= $categoriaActual === null ? 'activo' : '' ?>" href="productos.php">Todos</a>
                <?php foreach ($categoriasNav as $cat): ?>
                    <a class="chip <?= ($categoriaActual && $categoriaActual['slug'] === $cat['slug']) ? 'activo' : '' ?>" href="productos.php?cat=<?= e($cat['slug']) ?>"><?= e($cat['nombre']) ?></a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($productos)): ?>
                <p class="estado-vacio">Aún no hay productos en esta categoría.</p>
            <?php else: ?>
                <div class="grid-productos">
                    <?php foreach ($productos as $producto): ?>
                        <?= tarjeta_producto($producto) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
