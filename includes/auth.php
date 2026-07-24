<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

const NOMBRE_SESION_ADMIN = 'tps_admin_sesion';

/**
 * Arranca (o reanuda) la sesion de administracion con cookies seguras
 * y aplica expiracion por inactividad segun SESSION_LIFETIME.
 */
function sesion_iniciar(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_name(NOMBRE_SESION_ADMIN);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    if (isset($_SESSION['ultima_actividad']) && (time() - $_SESSION['ultima_actividad']) > SESSION_LIFETIME) {
        sesion_destruir_cookie();
        session_destroy();
        session_start();
    }

    $_SESSION['ultima_actividad'] = time();
}

function sesion_destruir_cookie(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $parametros['path'],
            $parametros['domain'],
            $parametros['secure'],
            $parametros['httponly']
        );
    }
}

function admin_autenticado(): bool
{
    return !empty($_SESSION['admin_id']);
}

/**
 * Redirige al login si no hay una sesion de administrador activa.
 */
function requerir_login(): void
{
    if (!admin_autenticado()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Marca la sesion como autenticada para el admin dado y regenera el ID de sesion.
 */
function login_admin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_usuario'] = $admin['usuario'];
    $_SESSION['admin_nombre'] = $admin['nombre'];
    $_SESSION['ultima_actividad'] = time();
}

function logout_admin(): void
{
    sesion_destruir_cookie();
    session_destroy();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_campo(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_validar(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verifica credenciales contra la tabla admins. Aplica una pequena pausa
 * en caso de fallo para dificultar ataques de fuerza bruta.
 */
function intentar_login(string $usuario, string $password): ?array
{
    global $mysqli;

    $stmt = $mysqli->prepare('SELECT id, usuario, password_hash, nombre FROM admins WHERE usuario = ? LIMIT 1');
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        usleep(500000);
        return null;
    }

    $actualizar = $mysqli->prepare('UPDATE admins SET ultimo_login = NOW() WHERE id = ?');
    $actualizar->bind_param('i', $admin['id']);
    $actualizar->execute();
    $actualizar->close();

    return $admin;
}

sesion_iniciar();
