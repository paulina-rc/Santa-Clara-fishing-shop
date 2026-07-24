<?php
declare(strict_types=1);

/**
 * Valida un archivo subido ($_FILES['imagen']). Comprueba error de subida,
 * tamano maximo y tipo MIME real (no el declarado por el navegador).
 */
function validar_imagen(array $archivo): array
{
    if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Ocurrió un error al subir la imagen.'];
    }

    if ($archivo['size'] > MAX_UPLOAD_SIZE) {
        return ['ok' => false, 'error' => 'La imagen supera el tamaño máximo permitido (3 MB).'];
    }

    $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $archivo['tmp_name']);

    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
        return ['ok' => false, 'error' => 'Formato de imagen no permitido. Usa JPG, PNG o WEBP.'];
    }

    return ['ok' => true, 'error' => ''];
}

/**
 * Guarda un archivo subido ya validado con un nombre seguro derivado del
 * MIME real y el id del producto: {id}-{slug}.{ext}
 * Devuelve el nombre final del archivo o false si no se pudo guardar.
 */
function guardar_imagen(array $archivo, int $id, string $nombre_base): string|false
{
    $extensionesPorMime = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $archivo['tmp_name']);

    if (!isset($extensionesPorMime[$mime])) {
        return false;
    }

    $slug = slugify($nombre_base);
    if ($slug === '') {
        $slug = 'producto';
    }

    $nombreArchivo = $id . '-' . $slug . '.' . $extensionesPorMime[$mime];
    $destino = UPLOADS_DIR . $nombreArchivo;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        return false;
    }

    chmod($destino, 0644);

    return $nombreArchivo;
}

/**
 * Borra del disco la imagen de un producto, si existe.
 */
function borrar_imagen(?string $nombre_archivo): void
{
    if ($nombre_archivo === null || $nombre_archivo === '') {
        return;
    }

    $ruta = UPLOADS_DIR . $nombre_archivo;

    if (is_file($ruta)) {
        unlink($ruta);
    }
}
