<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateExercisesTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150); // e.g., "Barbell Bench Press", "Squat"
            $table->enum('muscle_group', [
                'chest', 'back', 'shoulders', 'arms', 'legs', 
                'core', 'glutes', 'full_body', 'cardio'
            ]);
            $table->enum('equipment', [
                'barbell', 'dumbbell', 'machine', 'cables', 
                'bodyweight', 'bands', 'kettlebell', 'other'
            ])->default('barbell');
            $table->text('description')->nullable();
            $table->string('video_url')->nullable(); // Tutorial video
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('intermediate');
            $table->boolean('is_default')->default(false); // Preset exercises
            $table->unsignedBigInteger('created_by_user_id')->nullable(); // NULL for defaults
            $table->decimal('calories_per_minute', 5, 2)->nullable(); // Estimated calorie burn
            $table->timestamps();
            
            // Foreign key
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('muscle_group');
            $table->index('is_default');
            $table->index('created_by_user_id');
        });
        
        echo "    ✓ Created exercises table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('exercises');
        echo "    ✓ Dropped exercises table\n";
    }
}
