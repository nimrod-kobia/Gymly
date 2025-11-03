<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateSplitDayExercisesTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('split_day_exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('split_day_id');
            $table->unsignedBigInteger('exercise_id');
            $table->integer('target_sets')->default(3);
            $table->string('target_reps', 50)->default('8-12'); // Can be range or single number
            $table->integer('target_rest_seconds')->default(90); // Rest between sets
            $table->integer('display_order')->default(0);
            $table->text('notes')->nullable(); // e.g., "Go to failure on last set"
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('split_day_id')->references('id')->on('split_days')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            
            // Indexes
            $table->index('split_day_id');
            $table->index('exercise_id');
        });
        
        echo "    ✓ Created split_day_exercises table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('split_day_exercises');
        echo "    ✓ Dropped split_day_exercises table\n";
    }
}
