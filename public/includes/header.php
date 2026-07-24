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
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12c3-4 8-6 13-4 2 .8 4 2.4 5 4-1 1.6-3 3.2-5 4-5 2-10 0-13-4Z"/>
                <circle cx="8.2" cy="11.3" r=".6" fill="currentColor" stroke="none"/>
                <path d="M18 9.5 21 7M18 14.5 21 17"/>
            </svg>
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
        <span class="cabecera-icono" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="10.5" cy="10.5" r="6.5"/><line x1="20" y1="20" x2="15.4" y2="15.4"/></svg>
        </span>
        <span class="cabecera-icono" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.4"/><path d="M4.5 20c1.4-3.6 4.6-5.5 7.5-5.5s6.1 1.9 7.5 5.5"/></svg>
        </span>
        <a class="boton-whatsapp" href="<?= e(url_whatsapp('Hola, quiero más información sobre sus productos.')) ?>" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5.1-1.3A10 10 0 1 0 12 2Zm0 18.2a8.1 8.1 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1-.2.2-.7.8-.8.9-.2.2-.3.2-.5.1-.2-.1-1-.4-1.9-1.2-.7-.6-1.2-1.4-1.3-1.6-.1-.2 0-.4.1-.5.1-.1.2-.3.4-.4.1-.1.2-.3.2-.4.1-.2 0-.3 0-.5 0-.1-.6-1.5-.8-2-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.9.9-.9 2.2s1 2.6 1.1 2.7c.1.2 2 3 4.8 4.2.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
            <?= e(WHATSAPP_DISPLAY) ?>
        </a>
        <button type="button" class="cabecera-hamburguesa" id="botonMenuMovil" aria-label="Abrir menú" aria-expanded="false" aria-controls="navPrincipal">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
