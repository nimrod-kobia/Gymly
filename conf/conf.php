<?php
// Load Composer autoloader
require_once __DIR__ . "/../vendor/autoload.php";

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// Database configuration using NEON
define('DB_TYPE', $_ENV['DB_TYPE'] ?? 'pgsql'); 
define('DB_HOST', $_ENV['PGHOST'] ?? 'localhost');
define('DB_POOL_HOST', $_ENV['PGPOOLHOST'] ?? $_ENV['PGHOST_POOL'] ?? null);
define('DB_NAME', $_ENV['PGDATABASE'] ?? 'gymly');
define('DB_USER', $_ENV['PGUSER'] ?? 'user');
define('DB_PASS', $_ENV['PGPASSWORD'] ?? 'password');
define('DB_PORT', $_ENV['PGPORT'] ?? '5432'); 
define('DB_SSL_MODE', $_ENV['PGSSLMODE'] ?? 'require');
define('DB_CHANNEL_BINDING', $_ENV['PGCHANNELBINDING'] ?? 'require');
define('DB_CONN_TIMEOUT', (int)($_ENV['DB_CONN_TIMEOUT'] ?? 5));

// Session configuration - SECURITY FOCUSED
session_set_cookie_params([
    'lifetime' => 7200, // 2 hours
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    'secure' => isset($_SERVER['HTTPS']), // Only send over HTTPS
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict' // CSRF protection
]);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>