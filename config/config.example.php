<?php
/**
 * Copia este archivo como config.php y ajusta los valores para tu entorno.
 * config.php NO debe subirse al repositorio (ver .gitignore).
 */

declare(strict_types=1);

// Zona horaria del sitio
date_default_timezone_set('America/Costa_Rica');

// URL base del sitio (sin slash final)
define('BASE_URL', 'https://tiendapescasantaclara.com');

// Base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'tienda_pesca');
define('DB_USER', 'tienda_pesca_user');
define('DB_PASS', 'CAMBIAR_ESTA_CONTRASENA');
define('DB_CHARSET', 'utf8mb4');

// Contacto / redes sociales
define('WHATSAPP_NUMERO', '50688275387');   // formato internacional, solo digitos
define('WHATSAPP_DISPLAY', '8827-5387');    // formato para mostrar en pantalla
// define('INSTAGRAM_USUARIO', 'tiendapescasantaclara'); // sin cuenta actual
define('TIKTOK_USUARIO', 'tienda.de.pesca');
define('FACEBOOK_URL', 'https://www.facebook.com/share/198cm48Bwj/?mibextid=wwXIfr');
define('EMAIL_CONTACTO', 'contacto@tiendapescasantaclara.com');
define('DIRECCION', '500 metros de la plaza de deportes Santa Clara, Quesada, Costa Rica');
define('MAPS_URL', 'https://maps.app.goo.gl/LUt8kJrw9ahSnZev9');

// Subida de archivos (imagenes de productos)
define('UPLOADS_DIR', __DIR__ . '/../public/assets/uploads/');
define('UPLOADS_URL', BASE_URL . '/assets/uploads/');
define('MAX_UPLOAD_SIZE', 3 * 1024 * 1024); // 3 MB
define('EXTENSIONES_PERMITIDAS', ['jpg', 'jpeg', 'png', 'webp']);

// Sesion de administracion
define('SESSION_LIFETIME', 4 * 60 * 60); // 4 horas en segundos
