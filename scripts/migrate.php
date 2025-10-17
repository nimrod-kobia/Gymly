<?php
/**
 * Migration Runner
 * Usage: php scripts/migrate.php
 */

$capsule = require_once __DIR__ . '/bootstrap.php';

use Illuminate\Database\Schema\Blueprint;

// Create migrations table if it doesn't exist
if (!$capsule->schema()->hasTable('migrations')) {
    $capsule->schema()->create('migrations', function (Blueprint $table) {
        $table->increments('id');
        $table->string('migration');
        $table->integer('batch');
    });
    echo "Created migrations table\n";
}

// Get all migration files
$migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
sort($migrationFiles);

if (empty($migrationFiles)) {
    echo "  No migration files found in database/migrations/\n";
    exit(0);
}

// Get already run migrations
$ranMigrations = $capsule->table('migrations')
    ->orderBy('batch')
    ->pluck('migration')
    ->toArray();

// Get current batch number
$currentBatch = $capsule->table('migrations')->max('batch') ?? 0;
$nextBatch = $currentBatch + 1;

$migrationsToRun = [];

foreach ($migrationFiles as $file) {
    $migrationName = basename($file, '.php');
    
    if (!in_array($migrationName, $ranMigrations)) {
        $migrationsToRun[] = [
            'file' => $file,
            'name' => $migrationName
        ];
    }
}

if (empty($migrationsToRun)) {
    echo "Nothing to migrate - all migrations are up to date\n";
    exit(0);
}

echo " Running " . count($migrationsToRun) . " migration(s)...\n\n";

foreach ($migrationsToRun as $migration) {
    echo "⚡ Migrating: {$migration['name']}...";
    
    try {
        require_once $migration['file'];
        
        // Extract class name from file name
        // Example: 2024_10_17_000001_create_users_table.php -> CreateUsersTable
        $parts = explode('_', $migration['name']);
        array_shift($parts); // Remove date parts
        array_shift($parts);
        array_shift($parts);
        array_shift($parts);
        
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        if (class_exists($className)) {
            $migrationInstance = new $className;
            $migrationInstance->up();
            
            // Record migration
            $capsule->table('migrations')->insert([
                'migration' => $migration['name'],
                'batch' => $nextBatch
            ]);
            
            echo " DONE\n";
        } else {
            echo " FAILED (Class $className not found)\n";
        }
        
    } catch (Exception $e) {
        echo " FAILED\n";
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n All migrations completed successfully!\n";
