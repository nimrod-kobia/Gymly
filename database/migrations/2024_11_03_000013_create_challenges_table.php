<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateChallengesTable
{
    /**
     * Run the migrations.
     * 
     * Feature 12: Challenges & Goals
     */
    public function up()
    {
        Capsule::schema()->create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('challenge_name', 150); // e.g., "30-Day Workout Challenge"
            $table->text('description');
            $table->enum('challenge_type', [
                'workout_count', 'streak', 'total_volume', 
                'specific_exercise', 'duration', 'custom'
            ]);
            $table->enum('scope', ['global', 'personal']); // Global = everyone, Personal = user-specific
            $table->unsignedBigInteger('created_by_user_id')->nullable(); // NULL for system challenges
            $table->string('icon', 50)->default('🎯');
            $table->decimal('target_value', 10, 2); // Goal to reach (e.g., 30 workouts, 100kg squat)
            $table->string('unit', 50)->nullable(); // 'workouts', 'kg', 'minutes', 'days'
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('reward_points')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('scope');
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
        });
        
        echo "    ✓ Created challenges table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('challenges');
        echo "    ✓ Dropped challenges table\n";
    }
}
