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
 * Borra del disco un archivo de imagen de producto, si existe.
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

/**
 * Procesa la subida de multiples fotos para un producto ($_FILES['imagenes'],
 * con arrays paralelos name[]/type[]/tmp_name[]/error[]/size[]). Valida cada
 * archivo, respeta el limite de 10 fotos por producto y marca la primera
 * foto del producto como principal automaticamente.
 *
 * Devuelve ['agregadas' => int, 'errores' => string[]].
 */
function guardar_imagenes_multiples(array $archivos, int $producto_id, string $nombre_base): array
{
    global $mysqli;

    $totalArchivos = count($archivos['name'] ?? []);
    $hayArchivos = false;
    for ($i = 0; $i < $totalArchivos; $i++) {
        if (($archivos['name'][$i] ?? '') !== '') {
            $hayArchivos = true;
            break;
        }
    }

    if (!$hayArchivos) {
        return ['agregadas' => 0, 'errores' => []];
    }

    $limitePorProducto = 10;

    $stmt = $mysqli->prepare('SELECT COUNT(*) AS total FROM producto_imagenes WHERE producto_id = ?');
    $stmt->bind_param('i', $producto_id);
    $stmt->execute();
    $existentes = (int) $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    $espacioRestante = $limitePorProducto - $existentes;

    if ($espacioRestante <= 0) {
        return ['agregadas' => 0, 'errores' => ['Este producto ya tiene el máximo de 10 fotos.']];
    }

    $extensionesPorMime = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    $agregadas = 0;
    $errores = [];

    for ($i = 0; $i < $totalArchivos; $i++) {
        if (($archivos['name'][$i] ?? '') === '') {
            continue;
        }

        if ($agregadas >= $espacioRestante) {
            $errores[] = 'Se alcanzó el límite de 10 fotos por producto. Algunas fotos no se subieron.';
            break;
        }

        $archivoIndividual = [
            'name' => $archivos['name'][$i],
            'type' => $archivos['type'][$i],
            'tmp_name' => $archivos['tmp_name'][$i],
            'error' => $archivos['error'][$i],
            'size' => $archivos['size'][$i],
        ];

        $validacion = validar_imagen($archivoIndividual);
        if (!$validacion['ok']) {
            $errores[] = 'Archivo "' . $archivoIndividual['name'] . '": ' . $validacion['error'];
            continue;
        }

        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $archivoIndividual['tmp_name']);
        if (!isset($extensionesPorMime[$mime])) {
            $errores[] = 'Archivo "' . $archivoIndividual['name'] . '": formato no permitido.';
            continue;
        }

        $slug = slugify($nombre_base);
        if ($slug === '') {
            $slug = 'producto';
        }

        $nombreArchivo = $producto_id . '-' . $slug . '-' . time() . '-' . $i . '.' . $extensionesPorMime[$mime];
        $destino = UPLOADS_DIR . $nombreArchivo;

        if (!move_uploaded_file($archivoIndividual['tmp_name'], $destino)) {
            $errores[] = 'No se pudo guardar "' . $archivoIndividual['name'] . '".';
            continue;
        }

        chmod($destino, 0644);

        $esPrincipal = ($existentes + $agregadas === 0) ? 1 : 0;
        $orden = $existentes + $agregadas;

        $stmtInsert = $mysqli->prepare(
            'INSERT INTO producto_imagenes (producto_id, archivo, orden, es_principal) VALUES (?, ?, ?, ?)'
        );
        $stmtInsert->bind_param('isii', $producto_id, $nombreArchivo, $orden, $esPrincipal);
        $stmtInsert->execute();
        $stmtInsert->close();

        $agregadas++;
    }

    return ['agregadas' => $agregadas, 'errores' => $errores];
}

/**
 * Borra una foto especifica de un producto (BD + disco). Si era la
 * principal, marca automaticamente otra (la de menor orden) como principal.
 */
function borrar_imagen_producto(int $imagen_id): bool
{
    global $mysqli;

    $stmt = $mysqli->prepare('SELECT archivo, producto_id, es_principal FROM producto_imagenes WHERE id = ?');
    $stmt->bind_param('i', $imagen_id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$fila) {
        return false;
    }

    $stmtDelete = $mysqli->prepare('DELETE FROM producto_imagenes WHERE id = ?');
    $stmtDelete->bind_param('i', $imagen_id);
    $stmtDelete->execute();
    $stmtDelete->close();

    borrar_imagen($fila['archivo']);

    if ((int) $fila['es_principal'] === 1) {
        $stmtNueva = $mysqli->prepare(
            'UPDATE producto_imagenes SET es_principal = 1
             WHERE producto_id = ?
             ORDER BY orden ASC, id ASC
             LIMIT 1'
        );
        $stmtNueva->bind_param('i', $fila['producto_id']);
        $stmtNueva->execute();
        $stmtNueva->close();
    }

    return true;
}

/**
 * Marca una foto como principal y desmarca las demas del mismo producto.
 */
function marcar_imagen_principal(int $imagen_id): bool
{
    global $mysqli;

    $stmt = $mysqli->prepare('SELECT producto_id FROM producto_imagenes WHERE id = ?');
    $stmt->bind_param('i', $imagen_id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$fila) {
        return false;
    }

    $productoId = (int) $fila['producto_id'];

    $stmtDesmarcar = $mysqli->prepare('UPDATE producto_imagenes SET es_principal = 0 WHERE producto_id = ?');
    $stmtDesmarcar->bind_param('i', $productoId);
    $stmtDesmarcar->execute();
    $stmtDesmarcar->close();

    $stmtMarcar = $mysqli->prepare('UPDATE producto_imagenes SET es_principal = 1 WHERE id = ?');
    $stmtMarcar->bind_param('i', $imagen_id);
    $stmtMarcar->execute();
    $stmtMarcar->close();

    return true;
}
