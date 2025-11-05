<?php
/**
 * Simple PostgreSQL Migration Runner
 * Usage: php scripts/migrate_simple.php
 */

require_once __DIR__ . '/../autoload.php';

echo "=== Gymly Database Migration Tool ===\n\n";

try {
    $db = (new Database())->connect();
    echo "✓ Connected to database\n\n";
    
    // Create migrations tracking table if not exists
    $db->exec("
        CREATE TABLE IF NOT EXISTS migrations (
            id SERIAL PRIMARY KEY,
            migration VARCHAR(255) UNIQUE NOT NULL,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Get all migration files
    $migrationFiles = glob(__DIR__ . '/../database/migrations/*.php');
    sort($migrationFiles);
    
    if (empty($migrationFiles)) {
        echo "No migration files found.\n";
        exit(0);
    }
    
    // Get already run migrations
    $stmt = $db->query("SELECT migration FROM migrations");
    $ranMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get next batch number
    $stmt = $db->query("SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations");
    $nextBatch = $stmt->fetchColumn();
    
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
        echo "✓ All migrations are up to date.\n";
        exit(0);
    }
    
    echo "Found " . count($migrationsToRun) . " new migration(s):\n";
    foreach ($migrationsToRun as $migration) {
        echo "  - {$migration['name']}\n";
    }
    echo "\n";
    
    // Run migrations
    foreach ($migrationsToRun as $migration) {
        echo "Running: {$migration['name']}...\n";
        
        $migrationData = require $migration['file'];
        
        if (!is_array($migrationData) || !isset($migrationData['up'])) {
            echo "  ✗ Invalid migration format\n";
            continue;
        }
        
        try {
            // Execute the 'up' SQL
            $db->exec($migrationData['up']);
            
            // Record migration
            $stmt = $db->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
            $stmt->execute([$migration['name'], $nextBatch]);
            
            echo "  ✓ Success\n";
        } catch (Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
            echo "  Migration stopped.\n";
            exit(1);
        }
    }
    
    echo "\n✓ All migrations completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Database connection error: " . $e->getMessage() . "\n";
    exit(1);
}
