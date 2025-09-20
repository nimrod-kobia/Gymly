<?php
// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

// Database configuration using NEON
define('DB_TYPE', $_ENV['DB_TYPE'] ?? 'pgsql'); 
define('DB_HOST', $_ENV['PGHOST'] ?? 'localhost');
define('DB_NAME', $_ENV['PGDATABASE'] ?? 'gymly');
define('DB_USER', $_ENV['PGUSER'] ?? 'user');
define('DB_PASS', $_ENV['PGPASSWORD'] ?? 'password');
define('DB_PORT', $_ENV['PGPORT'] ?? '5432'); 
define('DB_SSL_MODE', $_ENV['PGSSLMODE'] ?? 'require');
define('DB_CHANNEL_BINDING', $_ENV['PGCHANNELBINDING'] ?? 'require');