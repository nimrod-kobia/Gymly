<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateTemplateExercisesTable
{
    /**
     * Run the migrations.
     * 
     * Exercises within workout templates
     */
    public function up()
    {
        Capsule::schema()->create('template_exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('exercise_id');
            $table->integer('sets')->default(3);
            $table->string('reps', 50)->default('8-12');
            $table->decimal('weight_kg', 8, 2)->nullable(); // Last used weight
            $table->integer('rest_seconds')->default(90);
            $table->integer('display_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('template_id')->references('id')->on('workout_templates')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            
            // Indexes
            $table->index('template_id');
        });
        
        echo "    ✓ Created template_exercises table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('template_exercises');
        echo "    ✓ Dropped template_exercises table\n";
    }
}
