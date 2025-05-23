<?php
/**
 * Fuerza el uso de HTTPS redirigiendo automáticamente si no está activo.
 */
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

/**
 * Carga de variables de entorno utilizando vlucas/phpdotenv.
 * Es fundamental para mantener claves sensibles fuera del código fuente.
 */
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

/**
 * Define claves criptográficas globales a partir de variables de entorno seguras.
 * SECRET_KEY se convierte desde hexadecimal para cumplir con sodium_crypto_secretbox().
 * NONCE se genera dinámicamente para cada sesión/uso.
 */
define('SECRET_KEY', sodium_hex2bin($_ENV['APP_SECRET_KEY']));
define('NONCE', random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES));

/**
 * Configuración de parámetros de sesión para reforzar la seguridad:
 * - Cookies seguras, HttpOnly y con SameSite estricto.
 * - Duración de la sesión: 24 horas (86400 segundos).
 */
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'secure' => true,
    'domain' => $_SERVER['HTTP_HOST'],
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Inicia la sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Establece una cookie segura adicional (no relacionada a la sesión PHP).
 * Esto puede ser útil para verificar la presencia de cookies seguras o para otros fines personalizados.
 */
setcookie(
    'mi_cookie_seguro',
    bin2hex(random_bytes(32)),
    [
        'expires'  => time() + 86400,
        'path'     => '/',
        'domain'   => $_SERVER['HTTP_HOST'],
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]
);

/**
 * Regeneración de ID de sesión:
 * - Se realiza al inicio o cada 30 minutos para mitigar ataques de fijación de sesión (session fixation).
 */
if (empty($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
