<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$query = trim((string) ($_GET['q'] ?? ''));
$productos = [];

if ($query !== '') {
    $termino = '%' . $query . '%';
    $stmt = $mysqli->prepare(
        'SELECT p.*, c.slug AS cat_slug, c.nombre AS cat_nombre
         FROM productos p
         JOIN categorias c ON p.categoria_id = c.id
         WHERE p.activo = 1
           AND (p.nombre LIKE ? OR p.marca LIKE ? OR p.descripcion LIKE ?)
         ORDER BY p.destacado DESC, p.actualizado_en DESC
         LIMIT 60'
    );
    $stmt->bind_param('sss', $termino, $termino, $termino);
    $stmt->execute();
    $productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$pagina_activa = 'buscar';
$meta_titulo = $query !== ''
    ? 'Búsqueda: ' . $query . ' · Tienda de Pesca Santa Clara'
    : 'Buscar · Tienda de Pesca Santa Clara';
$meta_descripcion = 'Busca productos de pesca en el catálogo de Tienda de Pesca Santa Clara.';

require __DIR__ . '/includes/header.php';
?>

<section class="seccion-buscar">
    <div class="contenedor">
        <h1 class="buscar-titulo">Buscar productos</h1>

        <form method="get" action="buscar.php" class="buscar-form" role="search">
            <div class="buscar-input-wrap">
                <svg class="buscar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="7"/>
                    <path d="M21 21l-4.3-4.3"/>
                </svg>
                <input
                    type="search"
                    name="q"
                    value="<?= e($query) ?>"
                    placeholder="¿Qué estás buscando? (ej. carrete, caña, señuelo…)"
                    autocomplete="off"
                    autofocus
                >
            </div>
            <button type="submit" class="buscar-btn">Buscar</button>
        </form>

        <?php if ($query !== ''): ?>
            <p class="buscar-resumen">
                <?php if (count($productos) === 0): ?>
                    Sin resultados para <strong><?= e($query) ?></strong>. Intenta con otras palabras o revisa el <a href="productos.php">catálogo completo</a>.
                <?php else: ?>
                    <?= count($productos) ?> resultado<?= count($productos) === 1 ? '' : 's' ?> para <strong><?= e($query) ?></strong>
                <?php endif; ?>
            </p>

            <?php if (count($productos) > 0): ?>
                <div class="grid-productos">
                    <?php foreach ($productos as $producto): ?>
                        <?= tarjeta_producto($producto) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="buscar-hint">Escribe el nombre de un producto, marca o categoría y presiona <strong>Buscar</strong>.</p>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
