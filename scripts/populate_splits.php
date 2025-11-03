<?php
/**
 * Populate workout splits with exercises
 */

$capsule = require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../database/seeds/WorkoutSeeder.php';

echo "🌱 Populating workout splits with exercises...\n\n";

try {
    $seeder = new WorkoutSeeder();
    
    // Call just the populate method using reflection
    $method = new ReflectionMethod('WorkoutSeeder', 'populateWorkoutSplitsWithExercises');
    $method->setAccessible(true);
    $method->invoke($seeder);
    
    echo "\n✅ Completed!\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
