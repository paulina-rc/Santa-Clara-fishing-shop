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
 * Devuelve la URL absoluta (via UPLOADS_URL) de la foto de un producto,
 * o null si no tiene imagen asignada o el archivo no existe en disco.
 * Absoluta para que funcione igual desde public/ y desde public/admin/.
 */
function producto_imagen_src(?string $imagen): ?string
{
    if ($imagen === null || $imagen === '') {
        return null;
    }

    if (!is_file(UPLOADS_DIR . $imagen)) {
        return null;
    }

    return UPLOADS_URL . rawurlencode($imagen);
}

/**
 * Devuelve el archivo de la foto principal de un producto (la marcada
 * es_principal, o la de menor orden si ninguna esta marcada), o null si
 * el producto no tiene fotos.
 */
function imagen_principal_producto(int $producto_id): ?string
{
    global $mysqli;

    $stmt = $mysqli->prepare(
        'SELECT archivo FROM producto_imagenes
         WHERE producto_id = ?
         ORDER BY es_principal DESC, orden ASC, id ASC
         LIMIT 1'
    );
    $stmt->bind_param('i', $producto_id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $fila['archivo'] ?? null;
}

/**
 * Devuelve todas las fotos de un producto en orden de despliegue
 * (principal primero, luego por orden y id).
 */
function imagenes_producto(int $producto_id): array
{
    global $mysqli;

    $stmt = $mysqli->prepare(
        'SELECT id, archivo, es_principal, orden FROM producto_imagenes
         WHERE producto_id = ?
         ORDER BY es_principal DESC, orden ASC, id ASC'
    );
    $stmt->bind_param('i', $producto_id);
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $filas;
}

/**
 * Genera el HTML de una tarjeta de producto (usada en home, listado y detalle).
 * Espera que la fila traiga la columna imagen_principal (via subquery en el SELECT).
 */
function tarjeta_producto(array $producto): string
{
    $imagenSrc = producto_imagen_src($producto['imagen_principal'] ?? null);

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
        'canas' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20 L18 5"/><circle cx="18" cy="5" r="1.4" fill="currentColor"/><path d="M14 9 L14 14"/><path d="M14 14 Q13 15.5 14 17 Q15 15.5 14 14"/></svg>',
        'carretes' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="3"/><path d="M12 5 L12 3"/><path d="M12 19 L12 21"/><circle cx="12" cy="3" r="0.8" fill="currentColor"/></svg>',
        'senuelos' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 L12 14"/><path d="M12 14 Q12 19 8 19 Q5 19 5 16"/><path d="M4 16 L6 16"/><circle cx="12" cy="3" r="0.8" fill="currentColor"/></svg>',
        'ropa' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 4 L5 6 L6 9 L8 8 L8 20 L16 20 L16 8 L18 9 L19 6 L16 4 L14 5 Q12 6.5 10 5 Z"/></svg>',
        'accesorios' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 9 L5 20 Q5 21 6 21 L18 21 Q19 21 19 20 L19 9 Z"/><path d="M9 9 L9 6 Q9 4 12 4 Q15 4 15 6 L15 9"/><path d="M5 13 L19 13"/></svg>',
        'ofertas' => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12 L12 20 L3 11 L3 4 L10 4 Z"/><circle cx="7.5" cy="7.5" r="1.2" fill="currentColor"/></svg>',
    ];

    return $iconos[$slug] ?? '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="7"/></svg>';
}
