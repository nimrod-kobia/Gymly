<?php
/**
 * Populate Full Body Split
 */

$capsule = require_once __DIR__ . '/bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;

echo "🌱 Populating Full Body split...\n\n";

try {
    $fullBodySplit = Capsule::table('workout_splits')->where('split_name', 'Full Body')->first();
    
    if (!$fullBodySplit) {
        die("Full Body split not found!\n");
    }
    
    echo "Found Full Body split (ID: {$fullBodySplit->id})\n";
    
    // Check if already populated
    $hasDays = Capsule::table('split_days')->where('split_id', $fullBodySplit->id)->exists();
    if ($hasDays) {
        die("Full Body split already has days!\n");
    }
    
    $days = ['Day A', 'Day B', 'Day C'];
    
    foreach ($days as $index => $dayName) {
        echo "Creating {$dayName}...\n";
        
        $dayId = Capsule::table('split_days')->insertGetId([
            'split_id' => $fullBodySplit->id,
            'day_name' => $dayName,
            'day_of_week' => ($index * 2) + 1, // Mon, Wed, Fri
            'is_rest_day' => false,
            'display_order' => $index + 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        echo "  Day ID: {$dayId}\n";
        
        // Full body exercises - covering all major muscle groups
        $exercises = [
            ['Barbell Squat', 4, '8-12', 180],          // Legs
            ['Barbell Bench Press', 4, '8-12', 120],    // Chest
            ['Barbell Row', 3, '8-12', 120],            // Back
            ['Overhead Press', 3, '8-10', 90],          // Shoulders
            ['Romanian Deadlift', 3, '10-12', 120],     // Hamstrings/Lower Back
            ['Pull-ups', 3, '6-10', 120],               // Back/Biceps
            ['Dumbbell Flyes', 3, '12-15', 60],         // Chest
            ['Lateral Raises', 3, '12-15', 60],         // Shoulders
            ['Plank', 3, '30-60s', 60],                 // Core
        ];
        
        echo "  Adding " . count($exercises) . " exercises...\n";
        
        foreach ($exercises as $exerciseIndex => $exercise) {
            $exerciseId = Capsule::table('exercises')
                ->where('name', $exercise[0])
                ->value('id');
            
            if ($exerciseId) {
                Capsule::table('split_day_exercises')->insert([
                    'split_day_id' => $dayId,
                    'exercise_id' => $exerciseId,
                    'target_sets' => $exercise[1],
                    'target_reps' => $exercise[2],
                    'target_rest_seconds' => $exercise[3],
                    'display_order' => $exerciseIndex + 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                echo "    ✓ {$exercise[0]}\n";
            } else {
                echo "    ✗ {$exercise[0]} not found\n";
            }
        }
    }
    
    echo "\n✅ Full Body split populated successfully!\n";
    
    // Show summary
    $totalDays = Capsule::table('split_days')->where('split_id', $fullBodySplit->id)->count();
    $totalExercises = Capsule::table('split_day_exercises')
        ->whereIn('split_day_id', function($q) use ($fullBodySplit) {
            $q->select('id')->from('split_days')->where('split_id', $fullBodySplit->id);
        })
        ->count();
    
    echo "\nSummary:\n";
    echo "  Days created: {$totalDays}\n";
    echo "  Exercises added: {$totalExercises}\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
