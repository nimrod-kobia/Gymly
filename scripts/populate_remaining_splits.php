<?php
/**
 * Populate Remaining Splits (Bro Split, Upper Lower, Push Pull Legs days 4-6)
 */

$capsule = require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../database/seeds/WorkoutSeeder.php';

use Illuminate\Database\Capsule\Manager as Capsule;

echo "🌱 Populating remaining workout splits...\n\n";

// Helper function to add exercises to a day
function addExercisesToDay($dayId, $exercises) {
    foreach ($exercises as $index => $exercise) {
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
                'display_order' => $index + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "      ✓ {$exercise[0]}\n";
        }
    }
}

try {
    // 1. Complete Push Pull Legs (add days 4-6)
    echo "1. Completing Push Pull Legs...\n";
    $ppl = Capsule::table('workout_splits')->where('split_name', 'Push Pull Legs')->first();
    $pplDays = Capsule::table('split_days')->where('split_id', $ppl->id)->count();
    
    if ($pplDays < 4) {
        echo "   Adding Push Day 2...\n";
        $pushDay2 = Capsule::table('split_days')->insertGetId([
            'split_id' => $ppl->id,
            'day_name' => 'Push Day',
            'day_of_week' => 4,
            'is_rest_day' => false,
            'display_order' => 4,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($pushDay2, [
            ['Incline Bench Press', 4, '8-10', 120],
            ['Dumbbell Bench Press', 3, '10-12', 90],
            ['Dumbbell Shoulder Press', 4, '8-12', 90],
            ['Cable Crossover', 3, '12-15', 60],
            ['Lateral Raises', 3, '12-15', 60],
            ['Overhead Tricep Extension', 3, '12-15', 60],
        ]);
        echo "   ✓ Push Day 2 complete\n\n";
    }
    
    // 2. Bro Split
    echo "2. Populating Bro Split...\n";
    $broSplit = Capsule::table('workout_splits')->where('split_name', 'Bro Split')->first();
    $broHasDays = Capsule::table('split_days')->where('split_id', $broSplit->id)->exists();
    
    if (!$broHasDays) {
        // Chest Day
        echo "   Creating Chest Day...\n";
        $chestDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $broSplit->id,
            'day_name' => 'Chest Day',
            'day_of_week' => 1,
            'is_rest_day' => false,
            'display_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($chestDay, [
            ['Barbell Bench Press', 4, '8-12', 120],
            ['Incline Dumbbell Press', 4, '10-12', 90],
            ['Decline Bench Press', 3, '10-12', 90],
            ['Dumbbell Flyes', 3, '12-15', 60],
            ['Cable Crossover', 3, '12-15', 60],
            ['Push-ups', 3, 'To Failure', 60],
        ]);
        
        // Back Day
        echo "   Creating Back Day...\n";
        $backDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $broSplit->id,
            'day_name' => 'Back Day',
            'day_of_week' => 2,
            'is_rest_day' => false,
            'display_order' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($backDay, [
            ['Deadlift', 4, '5-8', 180],
            ['Pull-ups', 4, '8-12', 120],
            ['Barbell Row', 4, '8-12', 120],
            ['Lat Pulldown', 3, '10-12', 90],
            ['Seated Cable Row', 3, '10-12', 90],
            ['Face Pulls', 3, '15-20', 60],
        ]);
        
        // Shoulder Day
        echo "   Creating Shoulder Day...\n";
        $shoulderDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $broSplit->id,
            'day_name' => 'Shoulder Day',
            'day_of_week' => 3,
            'is_rest_day' => false,
            'display_order' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($shoulderDay, [
            ['Overhead Press', 4, '8-10', 120],
            ['Dumbbell Shoulder Press', 3, '10-12', 90],
            ['Lateral Raises', 4, '12-15', 60],
            ['Front Raises', 3, '12-15', 60],
            ['Rear Delt Flyes', 3, '12-15', 60],
            ['Shrugs', 3, '12-15', 60],
        ]);
        
        // Arms Day
        echo "   Creating Arms Day...\n";
        $armsDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $broSplit->id,
            'day_name' => 'Arms Day',
            'day_of_week' => 4,
            'is_rest_day' => false,
            'display_order' => 4,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($armsDay, [
            ['Barbell Curl', 4, '10-12', 90],
            ['Hammer Curls', 3, '10-12', 60],
            ['Preacher Curls', 3, '10-12', 60],
            ['Tricep Dips', 4, '8-12', 90],
            ['Skull Crushers', 3, '10-12', 90],
            ['Cable Tricep Pushdown', 3, '12-15', 60],
        ]);
        
        // Leg Day
        echo "   Creating Leg Day...\n";
        $legDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $broSplit->id,
            'day_name' => 'Leg Day',
            'day_of_week' => 5,
            'is_rest_day' => false,
            'display_order' => 5,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($legDay, [
            ['Barbell Squat', 4, '8-12', 180],
            ['Romanian Deadlift', 4, '10-12', 120],
            ['Leg Press', 3, '12-15', 90],
            ['Leg Curl', 4, '12-15', 60],
            ['Leg Extension', 4, '12-15', 60],
            ['Calf Raises', 4, '15-20', 60],
        ]);
        
        echo "   ✓ Bro Split complete\n\n";
    }
    
    // 3. Upper Lower
    echo "3. Populating Upper Lower...\n";
    $upperLower = Capsule::table('workout_splits')->where('split_name', 'Upper Lower')->first();
    $ulHasDays = Capsule::table('split_days')->where('split_id', $upperLower->id)->exists();
    
    if (!$ulHasDays) {
        // Upper A
        echo "   Creating Upper Body A...\n";
        $upperA = Capsule::table('split_days')->insertGetId([
            'split_id' => $upperLower->id,
            'day_name' => 'Upper Body A',
            'day_of_week' => 1,
            'is_rest_day' => false,
            'display_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($upperA, [
            ['Barbell Bench Press', 4, '8-10', 120],
            ['Barbell Row', 4, '8-10', 120],
            ['Overhead Press', 3, '8-12', 90],
            ['Pull-ups', 3, '8-12', 90],
            ['Tricep Dips', 3, '10-12', 60],
            ['Barbell Curl', 3, '10-12', 60],
        ]);
        
        // Lower A
        echo "   Creating Lower Body A...\n";
        $lowerA = Capsule::table('split_days')->insertGetId([
            'split_id' => $upperLower->id,
            'day_name' => 'Lower Body A',
            'day_of_week' => 2,
            'is_rest_day' => false,
            'display_order' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($lowerA, [
            ['Barbell Squat', 4, '8-12', 180],
            ['Romanian Deadlift', 3, '10-12', 120],
            ['Leg Press', 3, '12-15', 90],
            ['Leg Curl', 3, '12-15', 60],
            ['Calf Raises', 4, '15-20', 60],
            ['Plank', 3, '30-60s', 60],
        ]);
        
        // Upper B
        echo "   Creating Upper Body B...\n";
        $upperB = Capsule::table('split_days')->insertGetId([
            'split_id' => $upperLower->id,
            'day_name' => 'Upper Body B',
            'day_of_week' => 4,
            'is_rest_day' => false,
            'display_order' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($upperB, [
            ['Incline Bench Press', 4, '8-10', 120],
            ['Lat Pulldown', 4, '10-12', 90],
            ['Dumbbell Shoulder Press', 3, '10-12', 90],
            ['Seated Cable Row', 3, '10-12', 90],
            ['Lateral Raises', 3, '12-15', 60],
            ['Face Pulls', 3, '15-20', 60],
        ]);
        
        // Lower B
        echo "   Creating Lower Body B...\n";
        $lowerB = Capsule::table('split_days')->insertGetId([
            'split_id' => $upperLower->id,
            'day_name' => 'Lower Body B',
            'day_of_week' => 5,
            'is_rest_day' => false,
            'display_order' => 4,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        addExercisesToDay($lowerB, [
            ['Deadlift', 4, '5-8', 180],
            ['Bulgarian Split Squat', 3, '10-12', 90],
            ['Leg Extension', 3, '12-15', 60],
            ['Leg Curl', 3, '12-15', 60],
            ['Calf Raises', 4, '15-20', 60],
            ['Hanging Leg Raises', 3, '10-15', 60],
        ]);
        
        echo "   ✓ Upper Lower complete\n\n";
    }
    
    echo "\n✅ All preset splits populated!\n\n";
    
    // Show final summary
    echo "Summary of all preset splits:\n";
    $splits = Capsule::table('workout_splits')->where('split_type', 'preset')->get(['id', 'split_name']);
    foreach ($splits as $split) {
        $days = Capsule::table('split_days')->where('split_id', $split->id)->count();
        $exercises = Capsule::table('split_day_exercises')
            ->whereIn('split_day_id', function($q) use ($split) {
                $q->select('id')->from('split_days')->where('split_id', $split->id);
            })
            ->count();
        echo "  • {$split->split_name}: {$days} days, {$exercises} exercises\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
