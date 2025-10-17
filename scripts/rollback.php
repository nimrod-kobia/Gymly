<?php
/**
 * Migration Rollback
 * Usage: php scripts/rollback.php [--step=N]
 */

$capsule = require_once __DIR__ . '/bootstrap.php';

use Illuminate\Database\Schema\Blueprint;

// Check if migrations table exists
if (!$capsule->schema()->hasTable('migrations')) {
    echo " No migrations table found. Nothing to rollback.\n";
    exit(0);
}

// Parse command line arguments
$options = getopt('', ['step::']);
$steps = isset($options['step']) ? (int)$options['step'] : 1;

// Get the last batch(es) to rollback
$lastBatch = $capsule->table('migrations')->max('batch');

if (!$lastBatch) {
    echo " No migrations to rollback\n";
    exit(0);
}

$batchesToRollback = [];
for ($i = 0; $i < $steps; $i++) {
    $batchesToRollback[] = $lastBatch - $i;
}

$migrationsToRollback = $capsule->table('migrations')
    ->whereIn('batch', $batchesToRollback)
    ->orderBy('id', 'desc')
    ->get();

if ($migrationsToRollback->isEmpty()) {
    echo " No migrations to rollback\n";
    exit(0);
}

echo " Rolling back " . $migrationsToRollback->count() . " migration(s)...\n\n";

foreach ($migrationsToRollback as $migration) {
    echo " Rolling back: {$migration->migration}...";
    
    try {
        $file = __DIR__ . '/../database/migrations/' . $migration->migration . '.php';
        
        if (!file_exists($file)) {
            echo "  FAILED (File not found)\n";
            continue;
        }
        
        require_once $file;
        
        // Extract class name
        $parts = explode('_', $migration->migration);
        array_shift($parts);
        array_shift($parts);
        array_shift($parts);
        array_shift($parts);
        
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        if (class_exists($className)) {
            $migrationInstance = new $className;
            $migrationInstance->down();
            
            // Remove from migrations table
            $capsule->table('migrations')
                ->where('id', $migration->id)
                ->delete();
            
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

echo "\n Rollback completed successfully!\n";
