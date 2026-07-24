<?php
declare(strict_types=1);

/**
 * Escapa texto para salida HTML segura.
 */
function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Formatea un monto como colones costarricenses (sin decimales, ej: ₡12.500).
 */
function precio(float $monto): string
{
    return '₡' . number_format($monto, 0, ',', '.');
}

/**
 * Convierte texto libre en un slug apto para URLs (minusculas, sin acentos, guiones).
 */
function slugify(string $texto): string
{
    $texto = mb_strtolower(trim($texto), 'UTF-8');
    $mapa = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ñ' => 'n', 'ü' => 'u',
    ];
    $texto = strtr($texto, $mapa);
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto) ?? '';

    return trim($texto, '-');
}

/**
 * Construye un enlace de WhatsApp (wa.me) con mensaje precargado opcional.
 */
function url_whatsapp(string $mensaje = ''): string
{
    $url = 'https://wa.me/' . WHATSAPP_NUMERO;

    if ($mensaje !== '') {
        $url .= '?text=' . rawurlencode($mensaje);
    }

    return $url;
}

/**
 * Guarda un mensaje flash en sesion para mostrarlo en la siguiente carga de pagina.
 */
function flash_set(string $tipo, string $mensaje): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

/**
 * Obtiene y limpia los mensajes flash pendientes.
 */
function flash_obtener(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION['flash'])) {
        return [];
    }

    $mensajes = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $mensajes;
}

/**
 * Renderiza los mensajes flash pendientes como HTML listo para imprimir.
 */
function flash_render(): string
{
    $html = '';

    foreach (flash_obtener() as $flash) {
        $clase = ($flash['tipo'] ?? '') === 'error' ? 'alerta alerta-error' : 'alerta alerta-exito';
        $html .= '<div class="' . e($clase) . '">' . e($flash['mensaje'] ?? '') . '</div>';
    }

    return $html;
}

/**
 * Recorta un texto a un largo maximo, agregando puntos suspensivos si aplica.
 */
function recortar(string $texto, int $limite): string
{
    return mb_strimwidth(trim($texto), 0, $limite, '…', 'UTF-8');
}

/**
 * Devuelve la ruta relativa (desde public/) de la foto de un producto,
 * o null si no tiene imagen asignada o el archivo no existe en disco.
 */
function producto_imagen_src(?string $imagen): ?string
{
    if ($imagen === null || $imagen === '') {
        return null;
    }

    if (!is_file(UPLOADS_DIR . $imagen)) {
        return null;
    }

    return 'assets/uploads/' . rawurlencode($imagen);
}

/**
 * Genera el HTML de una tarjeta de producto (usada en home, listado y detalle).
 */
function tarjeta_producto(array $producto): string
{
    $imagenSrc = producto_imagen_src($producto['imagen'] ?? null);

    if ($imagenSrc !== null) {
        $foto = '<img src="' . e($imagenSrc) . '" alt="' . e($producto['nombre']) . '" loading="lazy">';
    } else {
        $foto = '<div class="tarjeta-producto-sin-foto" aria-hidden="true">Sin foto</div>';
    }

    return '<a class="tarjeta-producto" href="producto.php?id=' . (int) $producto['id'] . '">'
        . '<div class="tarjeta-producto-foto">' . $foto . '</div>'
        . '<div class="tarjeta-producto-cuerpo">'
        . '<span class="tarjeta-producto-marca">' . e($producto['marca'] ?? '') . '</span>'
        . '<h3 class="tarjeta-producto-nombre">' . e($producto['nombre']) . '</h3>'
        . '<span class="tarjeta-producto-precio">' . precio((float) $producto['precio']) . '</span>'
        . '</div>'
        . '</a>';
}

/**
 * Devuelve el SVG (stroke currentColor) del icono asociado a un slug de categoria.
 */
function categoria_icono_svg(string $slug): string
{
    $iconos = [
        'canas' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><line x1="5" y1="19" x2="18" y2="6"/><circle cx="19.4" cy="4.6" r="1.6"/></svg>',
        'carretes' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="7.5"/><circle cx="12" cy="12" r="3"/></svg>',
        'senuelos' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 3v10"/><path d="M12 13a4 4 0 1 0 4 4c0-1.8-1.2-2.8-2.4-3.6"/><circle cx="12" cy="3" r="1.4" fill="currentColor" stroke="none"/></svg>',
        'ropa' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="M8 4 4 7l1.8 2.6L8 8.3V20h8V8.3l2.2 1.3L20 7l-4-3-2 1.6h-4L8 4Z"/></svg>',
        'accesorios' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="9" rx="1.5"/><path d="M8 11V7.5a4 4 0 0 1 8 0V11"/></svg>',
        'ofertas' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="M20 12.5 12.5 20a2.1 2.1 0 0 1-3 0L4 14.5a2.1 2.1 0 0 1 0-3L11.5 4H17a3 3 0 0 1 3 3v5.5Z"/><circle cx="15" cy="8" r="1.3" fill="currentColor" stroke="none"/></svg>',
    ];

    return $iconos[$slug] ?? '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="7"/></svg>';
}
