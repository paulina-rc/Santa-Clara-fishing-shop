<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$pagina_activa = 'home';
$meta_titulo = 'Tienda de Pesca Santa Clara · Cañas, carretes y señuelos en Costa Rica';
$meta_descripcion = 'Catálogo de equipo de pesca en Santa Clara, San Carlos: cañas, carretes, señuelos, ropa y accesorios. Consultá disponibilidad por WhatsApp.';

$stmtDestacados = $mysqli->prepare(
    'SELECT * FROM productos WHERE activo = 1 AND destacado = 1 ORDER BY actualizado_en DESC LIMIT 8'
);
$stmtDestacados->execute();
$destacados = $stmtDestacados->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtDestacados->close();

$heroFoto = null;
foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
    if (is_file(__DIR__ . '/assets/img/hero.' . $ext)) {
        $heroFoto = 'assets/img/hero.' . $ext;
        break;
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="contenedor">
        <div class="hero-texto">
            <p class="hero-eyebrow mono">Pasión · Paciencia · Conexión</p>
            <h1>Cada lanzamiento,<br>una nueva <span class="destacado">aventura</span>.</h1>
            <p class="hero-subtitulo">Equipo de pesca para río, lago y mar. Elegí lo que necesitás del catálogo y coordiná tu compra directo por WhatsApp.</p>
            <div class="hero-botones">
                <a class="boton boton-dorado" href="productos.php">Ver productos</a>
                <a class="boton boton-ghost-claro" href="<?= e(url_whatsapp('Hola, quiero más información sobre sus productos.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a>
            </div>
        </div>
        <div class="hero-visual">
            <?php if ($heroFoto !== null): ?>
                <img class="hero-photo" src="<?= e($heroFoto) ?>" alt="Pescador lanzando línea en Santa Clara">
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="seccion-categorias">
    <div class="contenedor">
        <div class="seccion-encabezado">
            <h2>¿En qué estás interesado?</h2>
            <span class="seccion-tag"><?= count($categoriasNav) ?> categorías</span>
        </div>

        <?php if (empty($categoriasNav)): ?>
            <p class="estado-vacio">Las categorías van a aparecer aquí muy pronto.</p>
        <?php else: ?>
            <div class="grid-categorias">
                <?php foreach ($categoriasNav as $cat): ?>
                    <a class="tarjeta-categoria" href="productos.php?cat=<?= e($cat['slug']) ?>">
                        <span class="tarjeta-categoria-icono" aria-hidden="true"><?= categoria_icono_svg($cat['slug']) ?></span>
                        <span class="tarjeta-categoria-nombre"><?= e($cat['nombre']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="seccion-destacados">
    <div class="contenedor">
        <div class="seccion-encabezado">
            <div>
                <h2>Más vendidos</h2>
                <p class="seccion-subtitulo">Lo que más se lleva la gente que pesca en Santa Clara</p>
            </div>
            <span class="seccion-tag">Destacados</span>
        </div>

        <?php if (empty($destacados)): ?>
            <p class="estado-vacio">Todavía no hay productos destacados. Muy pronto vas a ver aquí lo más vendido de la tienda.</p>
        <?php else: ?>
            <div class="grid-productos">
                <?php foreach ($destacados as $producto): ?>
                    <?= tarjeta_producto($producto) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="banda-contacto" id="contacto">
    <div class="contenedor">
        <div>
            <h3>Vive la pesca, disfruta la naturaleza.</h3>
            <p>Escribinos y te ayudamos a encontrar el equipo que necesitás.</p>
        </div>
        <a class="boton boton-dorado" href="<?= e(url_whatsapp('Hola, quiero más información sobre sus productos.')) ?>" target="_blank" rel="noopener">Escribir por WhatsApp</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
