<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateAchievementsTable
{
    /**
     * Run the migrations.
     * 
     * Feature 11: Achievement Badges
     */
    public function up()
    {
        Capsule::schema()->create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g., "7-Day Streak", "First Workout"
            $table->text('description');
            $table->string('icon', 50)->default('🏆'); // Emoji or icon class
            $table->enum('category', [
                'streak', 'volume', 'workout_count', 'pr', 
                'consistency', 'special', 'milestone'
            ]);
            $table->string('condition_type', 50); // e.g., "streak_days", "total_workouts", "total_volume_kg"
            $table->decimal('condition_value', 10, 2); // Threshold to unlock (e.g., 7 for 7-day streak)
            $table->integer('points')->default(10); // Gamification points
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');
            $table->timestamps();
            
            // Indexes
            $table->index('category');
            $table->index('condition_type');
        });
        
        echo "    ✓ Created achievements table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('achievements');
        echo "    ✓ Dropped achievements table\n";
    }
}
