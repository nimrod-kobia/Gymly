<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class WorkoutSeeder
{
    public function run()
    {
        echo "🌱 Seeding workout data...\n\n";
        
        $this->seedExercises();
        $this->seedWorkoutSplits();
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
        echo "  📝 Seeding workout splits...\n";
        
        // We'll create these as templates that can be copied to users
        // For now, just showing the structure - you'd assign these to users during onboarding
        
        echo "    ✓ Workout split templates ready (assign during user setup)\n";
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
