<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class WorkoutSeeder
{
    public function run()
    {
        echo "🌱 Seeding workout data...\n\n";
        
        $this->seedExercises();
        $this->seedWorkoutSplits();
        $this->populateWorkoutSplitsWithExercises();
        $this->seedAchievements();
        $this->seedChallenges();
        
        echo "\n✅ Workout seeding completed!\n";
    }
    
    /**
     * Seed default exercises
     */
    private function seedExercises()
    {
        echo "  📝 Seeding exercises...\n";
        
        $exercises = [
            // CHEST
            ['name' => 'Barbell Bench Press', 'muscle_group' => 'chest', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.5],
            ['name' => 'Dumbbell Bench Press', 'muscle_group' => 'chest', 'equipment' => 'dumbbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.2],
            ['name' => 'Incline Bench Press', 'muscle_group' => 'chest', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.4],
            ['name' => 'Decline Bench Press', 'muscle_group' => 'chest', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.3],
            ['name' => 'Dumbbell Flyes', 'muscle_group' => 'chest', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.5],
            ['name' => 'Cable Crossover', 'muscle_group' => 'chest', 'equipment' => 'cables', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 4.8],
            ['name' => 'Push-ups', 'muscle_group' => 'chest', 'equipment' => 'bodyweight', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.0],
            ['name' => 'Chest Dips', 'muscle_group' => 'chest', 'equipment' => 'bodyweight', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.0],
            
            // BACK
            ['name' => 'Deadlift', 'muscle_group' => 'back', 'equipment' => 'barbell', 'difficulty_level' => 'advanced', 'calories_per_minute' => 6.5],
            ['name' => 'Barbell Row', 'muscle_group' => 'back', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.5],
            ['name' => 'Pull-ups', 'muscle_group' => 'back', 'equipment' => 'bodyweight', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.2],
            ['name' => 'Lat Pulldown', 'muscle_group' => 'back', 'equipment' => 'machine', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.8],
            ['name' => 'Seated Cable Row', 'muscle_group' => 'back', 'equipment' => 'cables', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.5],
            ['name' => 'T-Bar Row', 'muscle_group' => 'back', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.3],
            ['name' => 'Dumbbell Row', 'muscle_group' => 'back', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.7],
            
            // LEGS
            ['name' => 'Barbell Squat', 'muscle_group' => 'legs', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 6.0],
            ['name' => 'Front Squat', 'muscle_group' => 'legs', 'equipment' => 'barbell', 'difficulty_level' => 'advanced', 'calories_per_minute' => 6.2],
            ['name' => 'Leg Press', 'muscle_group' => 'legs', 'equipment' => 'machine', 'difficulty_level' => 'beginner', 'calories_per_minute' => 5.0],
            ['name' => 'Romanian Deadlift', 'muscle_group' => 'legs', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.5],
            ['name' => 'Leg Curl', 'muscle_group' => 'legs', 'equipment' => 'machine', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.0],
            ['name' => 'Leg Extension', 'muscle_group' => 'legs', 'equipment' => 'machine', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.0],
            ['name' => 'Bulgarian Split Squat', 'muscle_group' => 'legs', 'equipment' => 'dumbbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.3],
            ['name' => 'Lunges', 'muscle_group' => 'legs', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.8],
            ['name' => 'Calf Raises', 'muscle_group' => 'legs', 'equipment' => 'machine', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.5],
            
            // SHOULDERS
            ['name' => 'Overhead Press', 'muscle_group' => 'shoulders', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 5.0],
            ['name' => 'Dumbbell Shoulder Press', 'muscle_group' => 'shoulders', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 4.8],
            ['name' => 'Lateral Raises', 'muscle_group' => 'shoulders', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.8],
            ['name' => 'Front Raises', 'muscle_group' => 'shoulders', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.8],
            ['name' => 'Rear Delt Flyes', 'muscle_group' => 'shoulders', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.5],
            ['name' => 'Face Pulls', 'muscle_group' => 'shoulders', 'equipment' => 'cables', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.5],
            
            // ARMS
            ['name' => 'Barbell Curl', 'muscle_group' => 'arms', 'equipment' => 'barbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.5],
            ['name' => 'Dumbbell Curl', 'muscle_group' => 'arms', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.5],
            ['name' => 'Hammer Curl', 'muscle_group' => 'arms', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.5],
            ['name' => 'Tricep Dips', 'muscle_group' => 'arms', 'equipment' => 'bodyweight', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 4.5],
            ['name' => 'Tricep Pushdown', 'muscle_group' => 'arms', 'equipment' => 'cables', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.8],
            ['name' => 'Skull Crushers', 'muscle_group' => 'arms', 'equipment' => 'barbell', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 4.0],
            ['name' => 'Overhead Tricep Extension', 'muscle_group' => 'arms', 'equipment' => 'dumbbell', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.8],
            
            // CORE
            ['name' => 'Plank', 'muscle_group' => 'core', 'equipment' => 'bodyweight', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.0],
            ['name' => 'Crunches', 'muscle_group' => 'core', 'equipment' => 'bodyweight', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.2],
            ['name' => 'Russian Twists', 'muscle_group' => 'core', 'equipment' => 'bodyweight', 'difficulty_level' => 'beginner', 'calories_per_minute' => 3.5],
            ['name' => 'Hanging Leg Raises', 'muscle_group' => 'core', 'equipment' => 'bodyweight', 'difficulty_level' => 'advanced', 'calories_per_minute' => 4.0],
            ['name' => 'Ab Wheel Rollout', 'muscle_group' => 'core', 'equipment' => 'other', 'difficulty_level' => 'advanced', 'calories_per_minute' => 4.5],
            
            // CARDIO
            ['name' => 'Treadmill Running', 'muscle_group' => 'cardio', 'equipment' => 'machine', 'difficulty_level' => 'beginner', 'calories_per_minute' => 8.0],
            ['name' => 'Cycling', 'muscle_group' => 'cardio', 'equipment' => 'machine', 'difficulty_level' => 'beginner', 'calories_per_minute' => 7.0],
            ['name' => 'Rowing Machine', 'muscle_group' => 'cardio', 'equipment' => 'machine', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 9.0],
            ['name' => 'Jump Rope', 'muscle_group' => 'cardio', 'equipment' => 'other', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 10.0],
            ['name' => 'Battle Ropes', 'muscle_group' => 'cardio', 'equipment' => 'other', 'difficulty_level' => 'intermediate', 'calories_per_minute' => 9.5],
        ];
        
        foreach ($exercises as $exercise) {
            $exercise['is_default'] = true;
            $exercise['created_at'] = now();
            $exercise['updated_at'] = now();
            Capsule::table('exercises')->insert($exercise);
        }
        
        echo "    ✓ Seeded " . count($exercises) . " exercises\n";
    }
    
    /**
     * Seed preset workout splits
     */
    private function seedWorkoutSplits()
    {
        echo "  📝 Seeding preset workout splits...\n";
        
        // Define preset splits
        $presetSplits = [
            [
                'split_name' => 'Push Pull Legs',
                'description' => '6-day training split focusing on push muscles (chest, shoulders, triceps), pull muscles (back, biceps), and legs. Excellent for intermediate to advanced lifters.',
            ],
            [
                'split_name' => 'Bro Split',
                'description' => '5-day split targeting one major muscle group per day (Chest, Back, Shoulders, Arms, Legs). Classic bodybuilding split.',
            ],
            [
                'split_name' => 'Upper Lower',
                'description' => '4-day split alternating between upper and lower body workouts. Great for strength and balanced development.',
            ],
            [
                'split_name' => 'Full Body',
                'description' => '3-day full body workout hitting all major muscle groups each session. Perfect for beginners or those with limited time.',
            ],
        ];
        
        $seededCount = 0;
        foreach ($presetSplits as $split) {
            // Check if this specific split exists
            $exists = Capsule::table('workout_splits')
                ->where('split_name', $split['split_name'])
                ->where('split_type', 'preset')
                ->exists();
            
            if (!$exists) {
                Capsule::table('workout_splits')->insert([
                    'split_name' => $split['split_name'],
                    'split_type' => 'preset',
                    'description' => $split['description'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $seededCount++;
            }
        }
        
        if ($seededCount > 0) {
            echo "    ✓ Seeded $seededCount preset workout splits\n";
        } else {
            echo "    ⚠ All preset splits already exist\n";
        }
    }
    
    /**
     * Populate workout splits with exercises
     */
    private function populateWorkoutSplitsWithExercises()
    {
        echo "  💪 Populating splits with exercises...\n";
        
        // Get all preset splits
        $splits = Capsule::table('workout_splits')
            ->where('split_type', 'preset')
            ->get();
        
        foreach ($splits as $split) {
            // Check if split already has days
            $hasDays = Capsule::table('split_days')
                ->where('split_id', $split->id)
                ->exists();
            
            if ($hasDays) {
                continue; // Skip if already populated
            }
            
            // Populate based on split name
            switch ($split->split_name) {
                case 'Full Body':
                    $this->populateFullBodySplit($split->id);
                    break;
                case 'Push Pull Legs':
                    $this->populatePushPullLegsSplit($split->id);
                    break;
                case 'Bro Split':
                    $this->populateBroSplit($split->id);
                    break;
                case 'Upper Lower':
                    $this->populateUpperLowerSplit($split->id);
                    break;
            }
        }
        
        echo "    ✓ Populated preset splits with exercises\n";
    }
    
    /**
     * Populate Full Body Split (3 days)
     */
    private function populateFullBodySplit($splitId)
    {
        $days = ['Day A', 'Day B', 'Day C'];
        
        foreach ($days as $index => $dayName) {
            $dayId = Capsule::table('split_days')->insertGetId([
                'split_id' => $splitId,
                'day_name' => $dayName,
                'day_of_week' => ($index * 2) + 1, // Mon, Wed, Fri
                'is_rest_day' => false,
                'display_order' => $index + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
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
            
            $this->addExercisesToDay($dayId, $exercises);
        }
    }
    
    /**
     * Populate Push Pull Legs Split (6 days)
     */
    private function populatePushPullLegsSplit($splitId)
    {
        // Push Day 1
        $pushDay1 = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Push Day',
            'day_of_week' => 1,
            'is_rest_day' => false,
            'display_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($pushDay1, [
            ['Barbell Bench Press', 4, '8-12', 120],
            ['Incline Dumbbell Press', 3, '10-12', 90],
            ['Overhead Press', 4, '8-10', 120],
            ['Lateral Raises', 3, '12-15', 60],
            ['Tricep Dips', 3, '8-12', 90],
            ['Cable Tricep Pushdown', 3, '12-15', 60],
        ]);
        
        // Pull Day
        $pullDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Pull Day',
            'day_of_week' => 2,
            'is_rest_day' => false,
            'display_order' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($pullDay, [
            ['Deadlift', 4, '5-8', 180],
            ['Pull-ups', 3, '8-12', 120],
            ['Barbell Row', 3, '8-12', 120],
            ['Lat Pulldown', 3, '10-12', 90],
            ['Face Pulls', 3, '15-20', 60],
            ['Barbell Curl', 3, '10-12', 60],
            ['Hammer Curls', 3, '10-12', 60],
        ]);
        
        // Leg Day
        $legDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Leg Day',
            'day_of_week' => 3,
            'is_rest_day' => false,
            'display_order' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($legDay, [
            ['Barbell Squat', 4, '8-12', 180],
            ['Romanian Deadlift', 3, '10-12', 120],
            ['Leg Press', 3, '12-15', 90],
            ['Leg Curl', 3, '12-15', 60],
            ['Leg Extension', 3, '12-15', 60],
            ['Calf Raises', 4, '15-20', 60],
        ]);
        
        // Repeat for days 4-6 (Push, Pull, Legs again)
        $pushDay2 = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Push Day',
            'day_of_week' => 4,
            'is_rest_day' => false,
            'display_order' => 4,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->addExercisesToDay($pushDay2, [
            ['Incline Bench Press', 4, '8-10', 120],
            ['Dumbbell Bench Press', 3, '10-12', 90],
            ['Dumbbell Shoulder Press', 4, '8-12', 90],
            ['Cable Crossover', 3, '12-15', 60],
            ['Lateral Raises', 3, '12-15', 60],
            ['Overhead Tricep Extension', 3, '12-15', 60],
        ]);
    }
    
    /**
     * Populate Bro Split (5 days)
     */
    private function populateBroSplit($splitId)
    {
        // Chest Day
        $chestDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Chest Day',
            'day_of_week' => 1,
            'is_rest_day' => false,
            'display_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($chestDay, [
            ['Barbell Bench Press', 4, '8-12', 120],
            ['Incline Dumbbell Press', 4, '10-12', 90],
            ['Decline Bench Press', 3, '10-12', 90],
            ['Dumbbell Flyes', 3, '12-15', 60],
            ['Cable Crossover', 3, '12-15', 60],
            ['Push-ups', 3, 'To Failure', 60],
        ]);
        
        // Back Day
        $backDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Back Day',
            'day_of_week' => 2,
            'is_rest_day' => false,
            'display_order' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($backDay, [
            ['Deadlift', 4, '5-8', 180],
            ['Pull-ups', 4, '8-12', 120],
            ['Barbell Row', 4, '8-12', 120],
            ['Lat Pulldown', 3, '10-12', 90],
            ['Seated Cable Row', 3, '10-12', 90],
            ['Face Pulls', 3, '15-20', 60],
        ]);
        
        // Shoulder Day
        $shoulderDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Shoulder Day',
            'day_of_week' => 3,
            'is_rest_day' => false,
            'display_order' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($shoulderDay, [
            ['Overhead Press', 4, '8-10', 120],
            ['Dumbbell Shoulder Press', 3, '10-12', 90],
            ['Lateral Raises', 4, '12-15', 60],
            ['Front Raises', 3, '12-15', 60],
            ['Rear Delt Flyes', 3, '12-15', 60],
            ['Shrugs', 3, '12-15', 60],
        ]);
        
        // Arms Day
        $armsDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Arms Day',
            'day_of_week' => 4,
            'is_rest_day' => false,
            'display_order' => 4,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($armsDay, [
            ['Barbell Curl', 4, '10-12', 90],
            ['Hammer Curls', 3, '10-12', 60],
            ['Preacher Curls', 3, '10-12', 60],
            ['Tricep Dips', 4, '8-12', 90],
            ['Skull Crushers', 3, '10-12', 90],
            ['Cable Tricep Pushdown', 3, '12-15', 60],
        ]);
        
        // Leg Day
        $legDay = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Leg Day',
            'day_of_week' => 5,
            'is_rest_day' => false,
            'display_order' => 5,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($legDay, [
            ['Barbell Squat', 4, '8-12', 180],
            ['Romanian Deadlift', 4, '10-12', 120],
            ['Leg Press', 3, '12-15', 90],
            ['Leg Curl', 4, '12-15', 60],
            ['Leg Extension', 4, '12-15', 60],
            ['Calf Raises', 4, '15-20', 60],
        ]);
    }
    
    /**
     * Populate Upper Lower Split (4 days)
     */
    private function populateUpperLowerSplit($splitId)
    {
        // Upper A
        $upperA = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Upper Body A',
            'day_of_week' => 1,
            'is_rest_day' => false,
            'display_order' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($upperA, [
            ['Barbell Bench Press', 4, '8-10', 120],
            ['Barbell Row', 4, '8-10', 120],
            ['Overhead Press', 3, '8-12', 90],
            ['Pull-ups', 3, '8-12', 90],
            ['Tricep Dips', 3, '10-12', 60],
            ['Barbell Curl', 3, '10-12', 60],
        ]);
        
        // Lower A
        $lowerA = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Lower Body A',
            'day_of_week' => 2,
            'is_rest_day' => false,
            'display_order' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($lowerA, [
            ['Barbell Squat', 4, '8-12', 180],
            ['Romanian Deadlift', 3, '10-12', 120],
            ['Leg Press', 3, '12-15', 90],
            ['Leg Curl', 3, '12-15', 60],
            ['Calf Raises', 4, '15-20', 60],
            ['Plank', 3, '30-60s', 60],
        ]);
        
        // Upper B
        $upperB = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Upper Body B',
            'day_of_week' => 4,
            'is_rest_day' => false,
            'display_order' => 3,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($upperB, [
            ['Incline Bench Press', 4, '8-10', 120],
            ['Lat Pulldown', 4, '10-12', 90],
            ['Dumbbell Shoulder Press', 3, '10-12', 90],
            ['Seated Cable Row', 3, '10-12', 90],
            ['Lateral Raises', 3, '12-15', 60],
            ['Face Pulls', 3, '15-20', 60],
        ]);
        
        // Lower B
        $lowerB = Capsule::table('split_days')->insertGetId([
            'split_id' => $splitId,
            'day_name' => 'Lower Body B',
            'day_of_week' => 5,
            'is_rest_day' => false,
            'display_order' => 4,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $this->addExercisesToDay($lowerB, [
            ['Deadlift', 4, '5-8', 180],
            ['Bulgarian Split Squat', 3, '10-12', 90],
            ['Leg Extension', 3, '12-15', 60],
            ['Leg Curl', 3, '12-15', 60],
            ['Calf Raises', 4, '15-20', 60],
            ['Hanging Leg Raises', 3, '10-15', 60],
        ]);
    }
    
    /**
     * Helper method to add exercises to a day
     */
    private function addExercisesToDay($dayId, $exercises)
    {
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
            }
        }
    }
    
    /**
     * Seed achievements
     */
    private function seedAchievements()
    {
        echo "  📝 Seeding achievements...\n";
        
        $achievements = [
            // STREAK ACHIEVEMENTS
            ['name' => '🔥 First Workout', 'description' => 'Complete your first workout', 'icon' => '🔥', 'category' => 'workout_count', 'condition_type' => 'total_workouts', 'condition_value' => 1, 'points' => 10, 'rarity' => 'common'],
            ['name' => '⚡ 3-Day Streak', 'description' => 'Workout for 3 days in a row', 'icon' => '⚡', 'category' => 'streak', 'condition_type' => 'streak_days', 'condition_value' => 3, 'points' => 20, 'rarity' => 'common'],
            ['name' => '🔥 7-Day Streak', 'description' => 'Workout for 7 days in a row', 'icon' => '🔥', 'category' => 'streak', 'condition_type' => 'streak_days', 'condition_value' => 7, 'points' => 50, 'rarity' => 'rare'],
            ['name' => '💪 14-Day Streak', 'description' => 'Workout for 14 days in a row', 'icon' => '💪', 'category' => 'streak', 'condition_type' => 'streak_days', 'condition_value' => 14, 'points' => 100, 'rarity' => 'epic'],
            ['name' => '👑 30-Day Streak', 'description' => 'Workout for 30 days in a row', 'icon' => '👑', 'category' => 'streak', 'condition_type' => 'streak_days', 'condition_value' => 30, 'points' => 250, 'rarity' => 'legendary'],
            
            // WORKOUT COUNT
            ['name' => '🎯 10 Workouts', 'description' => 'Complete 10 total workouts', 'icon' => '🎯', 'category' => 'workout_count', 'condition_type' => 'total_workouts', 'condition_value' => 10, 'points' => 30, 'rarity' => 'common'],
            ['name' => '💯 50 Workouts', 'description' => 'Complete 50 total workouts', 'icon' => '💯', 'category' => 'workout_count', 'condition_type' => 'total_workouts', 'condition_value' => 50, 'points' => 100, 'rarity' => 'rare'],
            ['name' => '🏆 100 Workouts', 'description' => 'Complete 100 total workouts', 'icon' => '🏆', 'category' => 'workout_count', 'condition_type' => 'total_workouts', 'condition_value' => 100, 'points' => 200, 'rarity' => 'epic'],
            ['name' => '⭐ 200 Workouts', 'description' => 'Complete 200 total workouts', 'icon' => '⭐', 'category' => 'workout_count', 'condition_type' => 'total_workouts', 'condition_value' => 200, 'points' => 400, 'rarity' => 'legendary'],
            
            // VOLUME MILESTONES
            ['name' => '💪 10,000kg Volume', 'description' => 'Lift a total of 10,000kg', 'icon' => '💪', 'category' => 'volume', 'condition_type' => 'total_volume_kg', 'condition_value' => 10000, 'points' => 50, 'rarity' => 'common'],
            ['name' => '🏋️ 50,000kg Volume', 'description' => 'Lift a total of 50,000kg', 'icon' => '🏋️', 'category' => 'volume', 'condition_type' => 'total_volume_kg', 'condition_value' => 50000, 'points' => 150, 'rarity' => 'rare'],
            ['name' => '💥 100,000kg Volume', 'description' => 'Lift a total of 100,000kg', 'icon' => '💥', 'category' => 'volume', 'condition_type' => 'total_volume_kg', 'condition_value' => 100000, 'points' => 300, 'rarity' => 'epic'],
            
            // TIME MILESTONES
            ['name' => '⏱️ 10 Hours Trained', 'description' => 'Train for 10 total hours', 'icon' => '⏱️', 'category' => 'milestone', 'condition_type' => 'total_minutes', 'condition_value' => 600, 'points' => 40, 'rarity' => 'common'],
            ['name' => '⏰ 50 Hours Trained', 'description' => 'Train for 50 total hours', 'icon' => '⏰', 'category' => 'milestone', 'condition_type' => 'total_minutes', 'condition_value' => 3000, 'points' => 120, 'rarity' => 'rare'],
            ['name' => '🕐 100 Hours Trained', 'description' => 'Train for 100 total hours', 'icon' => '🕐', 'category' => 'milestone', 'condition_type' => 'total_minutes', 'condition_value' => 6000, 'points' => 250, 'rarity' => 'epic'],
            
            // SPECIAL ACHIEVEMENTS
            ['name' => '🌅 Early Bird', 'description' => 'Complete a workout before 7 AM', 'icon' => '🌅', 'category' => 'special', 'condition_type' => 'workout_time_before', 'condition_value' => 7, 'points' => 25, 'rarity' => 'rare'],
            ['name' => '🌙 Night Owl', 'description' => 'Complete a workout after 9 PM', 'icon' => '🌙', 'category' => 'special', 'condition_type' => 'workout_time_after', 'condition_value' => 21, 'points' => 25, 'rarity' => 'rare'],
            ['name' => '📈 First PR', 'description' => 'Set your first personal record', 'icon' => '📈', 'category' => 'pr', 'condition_type' => 'pr_count', 'condition_value' => 1, 'points' => 30, 'rarity' => 'common'],
            ['name' => '🎖️ 10 PRs', 'description' => 'Set 10 personal records', 'icon' => '🎖️', 'category' => 'pr', 'condition_type' => 'pr_count', 'condition_value' => 10, 'points' => 100, 'rarity' => 'epic'],
        ];
        
        foreach ($achievements as $achievement) {
            $achievement['created_at'] = now();
            $achievement['updated_at'] = now();
            Capsule::table('achievements')->insert($achievement);
        }
        
        echo "    ✓ Seeded " . count($achievements) . " achievements\n";
    }
    
    /**
     * Seed global challenges
     */
    private function seedChallenges()
    {
        echo "  📝 Seeding challenges...\n";
        
        $challenges = [
            [
                'challenge_name' => '30-Day Workout Challenge',
                'description' => 'Complete 30 workouts in 30 days',
                'challenge_type' => 'workout_count',
                'scope' => 'global',
                'icon' => '🎯',
                'target_value' => 30,
                'unit' => 'workouts',
                'start_date' => date('Y-m-01'), // First day of current month
                'end_date' => date('Y-m-t'), // Last day of current month
                'reward_points' => 200,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'challenge_name' => 'November Fitness Streak',
                'description' => 'Maintain a 7-day workout streak',
                'challenge_type' => 'streak',
                'scope' => 'global',
                'icon' => '🔥',
                'target_value' => 7,
                'unit' => 'days',
                'start_date' => '2025-11-01',
                'end_date' => '2025-11-30',
                'reward_points' => 150,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'challenge_name' => 'Lift 50,000kg This Month',
                'description' => 'Total training volume challenge',
                'challenge_type' => 'total_volume',
                'scope' => 'global',
                'icon' => '💪',
                'target_value' => 50000,
                'unit' => 'kg',
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-t'),
                'reward_points' => 250,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($challenges as $challenge) {
            Capsule::table('challenges')->insert($challenge);
        }
        
        echo "    ✓ Seeded " . count($challenges) . " challenges\n";
    }
}

// Helper function for timestamps
function now() {
    return date('Y-m-d H:i:s');
}
