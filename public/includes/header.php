<?php
declare(strict_types=1);

if (!defined('BASE_URL')) {
    http_response_code(404);
    exit;
}

$pagina_activa = $pagina_activa ?? '';
$meta_titulo = $meta_titulo ?? 'Tienda de Pesca Santa Clara';
$meta_descripcion = $meta_descripcion ?? 'Cañas, carretes, señuelos, ropa y accesorios de pesca en Santa Clara, San Carlos, Costa Rica.';
$meta_imagen = $meta_imagen ?? '';
$queryActual = $_SERVER['QUERY_STRING'] ?? '';
$meta_url = $meta_url ?? rtrim(BASE_URL, '/') . '/' . basename($_SERVER['SCRIPT_NAME']) . ($queryActual !== '' ? '?' . $queryActual : '');

$categoriasNav = [];
$resultadoNav = $mysqli->query('SELECT slug, nombre FROM categorias WHERE activa = 1 ORDER BY orden');
if ($resultadoNav) {
    $categoriasNav = $resultadoNav->fetch_all(MYSQLI_ASSOC);
}

$logoSitio = is_file(__DIR__ . '/../assets/img/logo.png') ? 'assets/img/logo.png' : null;
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($meta_titulo) ?></title>
<meta name="description" content="<?= e($meta_descripcion) ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($meta_titulo) ?>">
<meta property="og:description" content="<?= e($meta_descripcion) ?>">
<meta property="og:url" content="<?= e($meta_url) ?>">
<?php if ($meta_imagen !== ''): ?>
<meta property="og:image" content="<?= e($meta_imagen) ?>">
<?php endif; ?>
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(BASE_URL) ?>/assets/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= e(BASE_URL) ?>/assets/img/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?= e(BASE_URL) ?>/assets/img/apple-touch-icon.png">
<link rel="icon" type="image/x-icon" href="<?= e(BASE_URL) ?>/assets/img/favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/publico.css">
</head>
<body>

<div class="promo-barra">Envíos a todo Costa Rica · Retiro en Santa Clara, San Carlos</div>

<header class="cabecera">
    <a class="cabecera-logo" href="index.php">
        <span class="cabecera-logo-insignia" aria-hidden="true">
            <?php if ($logoSitio !== null): ?>
                <img src="<?= e($logoSitio) ?>" alt="" class="cabecera-logo-img">
            <?php else: ?>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12c3-4 8-6 13-4 2 .8 4 2.4 5 4-1 1.6-3 3.2-5 4-5 2-10 0-13-4Z"/>
                    <circle cx="8.2" cy="11.3" r=".6" fill="currentColor" stroke="none"/>
                    <path d="M18 9.5 21 7M18 14.5 21 17"/>
                </svg>
            <?php endif; ?>
        </span>
        <span class="cabecera-logo-texto">
            <span class="cabecera-logo-nombre">Tienda de Pesca</span>
            <span class="cabecera-logo-lugar">Santa Clara · CR</span>
        </span>
    </a>

    <?php $nosotrosActivo = in_array($pagina_activa, ['nosotros', 'contacto'], true); ?>
    <nav class="cabecera-nav" id="navPrincipal">
        <a href="productos.php" class="<?= $pagina_activa === 'catalogo' ? 'activo' : '' ?>">Catálogo</a>
        <a href="nosotros.php" class="<?= $nosotrosActivo ? 'activo' : '' ?>">Sobre nosotros</a>
        <a href="nosotros.php#contacto">Contacto</a>
    </nav>

    <div class="cabecera-acciones">
        <a class="cabecera-icono <?= $pagina_activa === 'buscar' ? 'activo' : '' ?>" href="buscar.php" aria-label="Buscar productos">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="6.5"/><line x1="20" y1="20" x2="15.4" y2="15.4"/></svg>
        </a>
        <a class="boton-whatsapp" href="<?= e(url_whatsapp('Hola, quiero más información sobre sus productos.')) ?>" target="_blank" rel="noopener">
            <svg viewBox="0 0 32 32" width="24" height="24" fill="#fff" xmlns="http://www.w3.org/2000/svg"><path d="M16.001 3C9.373 3 4 8.373 4 15c0 2.117.554 4.108 1.522 5.833L4 27l6.336-1.499A11.94 11.94 0 0016.001 27C22.628 27 28 21.627 28 15S22.628 3 16.001 3zm0 21.6c-1.816 0-3.517-.494-4.976-1.351l-.357-.212-3.762.89.895-3.665-.233-.376A9.548 9.548 0 016.4 15c0-5.293 4.308-9.6 9.601-9.6 5.293 0 9.6 4.307 9.6 9.6 0 5.293-4.307 9.6-9.6 9.6zm5.276-7.194c-.29-.145-1.714-.845-1.979-.94-.265-.096-.458-.145-.65.146-.194.29-.746.94-.915 1.134-.169.194-.338.218-.628.073-.29-.145-1.226-.452-2.335-1.44-.863-.769-1.446-1.72-1.615-2.01-.169-.29-.018-.446.127-.591.13-.129.29-.338.435-.507.145-.169.194-.29.29-.483.097-.194.048-.363-.024-.508-.073-.145-.65-1.568-.891-2.148-.234-.564-.472-.488-.65-.497l-.554-.01a1.06 1.06 0 00-.77.362c-.264.29-1.007.985-1.007 2.402 0 1.417 1.031 2.787 1.175 2.98.145.193 2.032 3.098 4.925 4.345.688.297 1.225.474 1.643.607.69.219 1.319.188 1.815.114.554-.083 1.714-.7 1.957-1.376.242-.677.242-1.257.169-1.377-.073-.12-.264-.194-.554-.339z"/></svg>
            <?= e(WHATSAPP_DISPLAY) ?>
        </a>
        <button type="button" class="cabecera-hamburguesa" id="botonMenuMovil" aria-label="Abrir menú" aria-expanded="false" aria-controls="navPrincipal">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
