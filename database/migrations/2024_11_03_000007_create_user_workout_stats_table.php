<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateUserWorkoutStatsTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('user_workout_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->integer('current_streak_days')->default(0);
            $table->integer('longest_streak_days')->default(0);
            $table->integer('total_workouts_completed')->default(0);
            $table->integer('total_minutes_trained')->default(0);
            $table->decimal('total_calories_burned', 10, 2)->default(0);
            $table->decimal('total_volume_kg', 12, 2)->default(0); // Total weight × reps × sets
            $table->date('last_workout_date')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('current_streak_days');
            $table->index('total_workouts_completed');
        });
        
        echo "    ✓ Created user_workout_stats table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('user_workout_stats');
        echo "    ✓ Dropped user_workout_stats table\n";
    }
}
