<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkoutTemplatesTable
{
    /**
     * Run the migrations.
     * 
     * Feature 3: Workout Templates & Quick Start
     */
    public function up()
    {
        Capsule::schema()->create('workout_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('template_name', 150); // e.g., "My Chest Day", "Quick Upper Body"
            $table->text('description')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->integer('times_used')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('user_id');
            $table->index(['user_id', 'is_favorite']);
        });
        
        echo "    ✓ Created workout_templates table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('workout_templates');
        echo "    ✓ Dropped workout_templates table\n";
    }
}
