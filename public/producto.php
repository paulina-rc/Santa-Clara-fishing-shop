<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$producto = null;

if ($id !== null && $id !== false) {
    $stmt = $mysqli->prepare(
        'SELECT p.*, c.slug AS cat_slug, c.nombre AS cat_nombre,
                s.slug AS sub_slug, s.nombre AS sub_nombre
         FROM productos p
         JOIN categorias c ON p.categoria_id = c.id
         LEFT JOIN subcategorias s ON p.subcategoria_id = s.id
         WHERE p.id = ? AND p.activo = 1
         LIMIT 1'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$producto) {
    http_response_code(404);
    $pagina_activa = 'catalogo';
    $meta_titulo = 'Producto no encontrado · Tienda de Pesca Santa Clara';
    $meta_descripcion = 'El producto que buscás no está disponible.';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="seccion-listado">
        <div class="contenedor">
            <div class="estado-vacio">
                <p>No encontramos este producto. Puede que ya no esté disponible.</p>
                <p><a class="boton boton-ghost" href="productos.php">Ver todos los productos</a></p>
            </div>
        </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$imagenes = imagenes_producto((int) $producto['id']);
$imagenPrincipal = imagen_principal_producto((int) $producto['id']);
$precioFormateado = precio((float) $producto['precio']);
$urlCatalogoProducto = 'productos.php?cat=' . rawurlencode($producto['cat_slug'])
    . (!empty($producto['sub_slug']) ? '&sub=' . rawurlencode($producto['sub_slug']) : '');

$pagina_activa = 'catalogo';
$meta_titulo = $producto['nombre'] . ' · Tienda de Pesca Santa Clara';
$meta_descripcion = $producto['descripcion'] !== null && $producto['descripcion'] !== ''
    ? recortar($producto['descripcion'], 155)
    : 'Conocé ' . $producto['nombre'] . ' en Tienda de Pesca Santa Clara. Consultá disponibilidad y precio por WhatsApp.';
$meta_imagen = $imagenPrincipal !== null ? (producto_imagen_src($imagenPrincipal) ?? '') : '';

$stmtRelacionados = $mysqli->prepare(
    'SELECT p.*,
            (SELECT archivo FROM producto_imagenes
             WHERE producto_id = p.id
             ORDER BY es_principal DESC, orden ASC, id ASC
             LIMIT 1) AS imagen_principal
     FROM productos p
     WHERE p.activo = 1 AND p.categoria_id = ? AND p.id <> ?
     ORDER BY RAND() LIMIT 4'
);
$stmtRelacionados->bind_param('ii', $producto['categoria_id'], $producto['id']);
$stmtRelacionados->execute();
$relacionados = $stmtRelacionados->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtRelacionados->close();

require __DIR__ . '/includes/header.php';
?>

<p class="migaja contenedor">
    <a href="index.php">Inicio</a> ›
    <a href="productos.php?cat=<?= e($producto['cat_slug']) ?>"><?= e($producto['cat_nombre']) ?></a> ›
    <?php if (!empty($producto['sub_slug'])): ?>
        <a href="<?= e($urlCatalogoProducto) ?>"><?= e($producto['sub_nombre']) ?></a> ›
    <?php endif; ?>
    <?= e(recortar($producto['nombre'], 40)) ?>
</p>

<section class="detalle-producto contenedor">
    <div class="galeria">
        <?php if (count($imagenes) === 0): ?>
            <div class="galeria-principal galeria-vacia">
                <div class="tarjeta-producto-sin-foto" aria-hidden="true">Sin foto</div>
            </div>
        <?php else: ?>
            <div class="galeria-principal" data-total="<?= count($imagenes) ?>">
                <?php if (count($imagenes) > 1): ?>
                    <button type="button" class="galeria-flecha galeria-anterior" aria-label="Foto anterior">‹</button>
                    <button type="button" class="galeria-flecha galeria-siguiente" aria-label="Foto siguiente">›</button>
                <?php endif; ?>

                <?php foreach ($imagenes as $idx => $img): ?>
                    <?php $fotoSrc = producto_imagen_src($img['archivo']); ?>
                    <?php if ($fotoSrc !== null): ?>
                        <img
                            src="<?= e($fotoSrc) ?>"
                            alt="<?= e($producto['nombre']) ?> - foto <?= $idx + 1 ?>"
                            class="galeria-foto <?= $idx === 0 ? 'activa' : '' ?>"
                            data-index="<?= $idx ?>">
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if (count($imagenes) > 1): ?>
                    <div class="galeria-contador">
                        <span class="galeria-actual">1</span> / <?= count($imagenes) ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (count($imagenes) > 1): ?>
                <div class="galeria-miniaturas">
                    <?php foreach ($imagenes as $idx => $img): ?>
                        <?php $miniSrc = producto_imagen_src($img['archivo']); ?>
                        <?php if ($miniSrc !== null): ?>
                            <button type="button"
                                    class="galeria-mini <?= $idx === 0 ? 'activa' : '' ?>"
                                    data-index="<?= $idx ?>"
                                    aria-label="Ver foto <?= $idx + 1 ?>">
                                <img src="<?= e($miniSrc) ?>" alt="Miniatura <?= $idx + 1 ?>">
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="detalle-info">
        <?php if (!empty($producto['marca'])): ?>
            <span class="detalle-marca mono"><?= e($producto['marca']) ?></span>
        <?php endif; ?>
        <h1 class="detalle-nombre"><?= e($producto['nombre']) ?></h1>
        <span class="detalle-precio"><?= $precioFormateado ?></span>

        <?php if (!empty($producto['descripcion'])): ?>
            <div class="detalle-descripcion"><?= nl2br(e($producto['descripcion'])) ?></div>
        <?php endif; ?>

        <div class="detalle-botones">
            <a class="boton boton-dorado" href="<?= e(url_whatsapp('Hola, me gustaría consultar por el producto: ' . $producto['nombre'] . ' — ' . $precioFormateado . '. ¿Tienen disponible?')) ?>" target="_blank" rel="noopener">Consultar por WhatsApp</a>
            <a class="boton boton-ghost" href="<?= e($urlCatalogoProducto) ?>">Ver más productos</a>
        </div>
    </div>
</section>

<?php if (!empty($relacionados)): ?>
<section class="seccion-relacionados">
    <div class="contenedor">
        <div class="seccion-encabezado">
            <h2>También te puede interesar</h2>
        </div>
        <div class="grid-productos">
            <?php foreach ($relacionados as $item): ?>
                <?= tarjeta_producto($item) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
