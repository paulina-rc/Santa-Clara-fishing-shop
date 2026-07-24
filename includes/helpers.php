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
