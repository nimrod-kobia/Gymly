<?php
/**
 * Migration Status Check
 * Usage: php scripts/status.php
 */

$capsule = require_once __DIR__ . '/bootstrap.php';

echo "Migration Status\n";
echo "==================\n\n";

// Check if migrations table exists
if (!$capsule->schema()->hasTable('migrations')) {
    echo " Migrations table doesn't exist yet.\n";
    echo "   Run: php scripts/migrate.php\n";
    exit(0);
}

// Get all migrations that have been run
$ranMigrations = $capsule->table('migrations')
    ->orderBy('batch')
    ->orderBy('id')
    ->get();

if ($ranMigrations->isEmpty()) {
    echo "No migrations have been run yet.\n";
    echo "   Run: php scripts/migrate.php\n";
    exit(0);
}

echo "Migrations Run:\n\n";

$currentBatch = null;
foreach ($ranMigrations as $migration) {
    if ($currentBatch !== $migration->batch) {
        $currentBatch = $migration->batch;
        echo "Batch {$currentBatch}:\n";
    }
    echo "   {$migration->migration}\n";
}

echo "\n";

// Check for pending migrations
$migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
$ranMigrationNames = $ranMigrations->pluck('migration')->toArray();
$pendingMigrations = [];

foreach ($migrationFiles as $file) {
    $migrationName = basename($file, '.php');
    if (!in_array($migrationName, $ranMigrationNames)) {
        $pendingMigrations[] = $migrationName;
    }
}

if (!empty($pendingMigrations)) {
    echo "Pending Migrations:\n\n";
    foreach ($pendingMigrations as $pending) {
        echo "  • {$pending}\n";
    }
    echo "\n";
    echo "Run: php scripts/migrate.php\n";
} else {
    echo "All migrations are up to date!\n";
}
