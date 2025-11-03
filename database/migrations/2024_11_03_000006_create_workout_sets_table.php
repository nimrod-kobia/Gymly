<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkoutSetsTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('workout_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workout_session_id');
            $table->unsignedBigInteger('exercise_id');
            $table->integer('set_number'); // 1, 2, 3...
            $table->integer('reps_completed');
            $table->decimal('weight_kg', 8, 2)->default(0); // Weight lifted
            $table->integer('rest_seconds')->nullable(); // Actual rest taken
            $table->tinyInteger('rpe')->nullable(); // Rate of Perceived Exertion (1-10)
            $table->boolean('is_warmup')->default(false); // Mark as warm-up set
            $table->text('notes')->nullable(); // e.g., "Felt heavy", "Easy"
            $table->timestamp('created_at')->useCurrent();
            
            // Foreign keys
            $table->foreign('workout_session_id')->references('id')->on('workout_sessions')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            
            // Indexes
            $table->index('workout_session_id');
            $table->index('exercise_id');
            $table->index(['exercise_id', 'created_at']); // For progression tracking
        });
        
        echo "    ✓ Created workout_sets table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('workout_sets');
        echo "    ✓ Dropped workout_sets table\n";
    }
}
