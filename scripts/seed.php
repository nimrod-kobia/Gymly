<?php
/**
 * Database Seeder Runner
 * Usage: php scripts/seed.php [SeederClassName]
 */

$capsule = require_once __DIR__ . '/bootstrap.php';

// Get seeder class name from command line argument
$seederClass = $argv[1] ?? 'DatabaseSeeder';

$seederFile = __DIR__ . '/../database/seeds/' . $seederClass . '.php';

if (!file_exists($seederFile)) {
    echo " Seeder file not found: {$seederFile}\n";
    echo "Usage: php scripts/seed.php [SeederClassName]\n";
    exit(1);
}

require_once $seederFile;

if (!class_exists($seederClass)) {
    echo "Seeder class '{$seederClass}' not found\n";
    exit(1);
}

echo "Seeding database with {$seederClass}...\n\n";

try {
    $seeder = new $seederClass;
    $seeder->run();
    echo "\n Database seeded successfully!\n";
} catch (Exception $e) {
    echo "\n Seeding failed: " . $e->getMessage() . "\n";
    exit(1);
}
