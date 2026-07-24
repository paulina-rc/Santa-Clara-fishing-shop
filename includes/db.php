<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset(DB_CHARSET);
} catch (mysqli_sql_exception $e) {
    error_log('Error de conexion a la base de datos: ' . $e->getMessage());
    http_response_code(500);
    die('No fue posible conectar con la base de datos. Intenta de nuevo mas tarde.');
}
