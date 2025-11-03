<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateUserGoalsTable
{
    /**
     * Run the migrations.
     * 
     * Personal fitness goals
     */
    public function up()
    {
        Capsule::schema()->create('user_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('goal_name', 150); // e.g., "Squat 100kg", "Lose 5kg"
            $table->enum('goal_type', [
                'weight_loss', 'muscle_gain', 'strength', 
                'endurance', 'exercise_pr', 'consistency', 'custom'
            ]);
            $table->unsignedBigInteger('exercise_id')->nullable(); // If goal is exercise-specific
            $table->decimal('target_value', 10, 2);
            $table->decimal('current_value', 10, 2)->default(0);
            $table->string('unit', 50); // 'kg', 'workouts', 'minutes', 'reps'
            $table->date('target_date')->nullable();
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('set null');
            
            // Indexes
            $table->index('user_id');
            $table->index(['user_id', 'status']);
        });
        
        echo "    ✓ Created user_goals table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('user_goals');
        echo "    ✓ Dropped user_goals table\n";
    }
}
