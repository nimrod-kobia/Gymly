<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateExercisePrsTable
{
    /**
     * Run the migrations.
     * 
     * Track Personal Records for progressive overload
     */
    public function up()
    {
        Capsule::schema()->create('exercise_prs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('exercise_id');
            $table->enum('pr_type', ['max_weight', 'max_reps', 'max_volume', 'max_1rm']); // Type of PR
            $table->decimal('value', 10, 2); // Weight in kg, reps count, or calculated 1RM
            $table->integer('reps')->nullable(); // Reps at which PR was achieved
            $table->date('achieved_date');
            $table->unsignedBigInteger('workout_session_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->foreign('workout_session_id')->references('id')->on('workout_sessions')->onDelete('set null');
            
            // Indexes
            $table->index(['user_id', 'exercise_id']);
            $table->index('achieved_date');
        });
        
        echo "    ✓ Created exercise_prs table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('exercise_prs');
        echo "    ✓ Dropped exercise_prs table\n";
    }
}
