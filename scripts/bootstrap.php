<?php
/**
 * Bootstrap file for Illuminate Database migrations
 * Sets up the database connection and schema builder
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => $_ENV['PGHOST'] ?? 'localhost',
    'port'      => $_ENV['PGPORT'] ?? '5432',
    'database'  => $_ENV['PGDATABASE'] ?? 'gymly',
    'username'  => $_ENV['PGUSER'] ?? 'user',
    'password'  => $_ENV['PGPASSWORD'] ?? 'password',
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'    => 'public',
    'sslmode'   => $_ENV['PGSSLMODE'] ?? 'prefer',
]);

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();

return $capsule;
