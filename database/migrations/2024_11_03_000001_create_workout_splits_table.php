<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkoutSplitsTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('workout_splits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('split_name', 100); // e.g., "Push Pull Legs", "Bro Split"
            $table->enum('split_type', ['preset', 'custom'])->default('custom');
            $table->boolean('is_active')->default(false); // Only one active split per user
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('user_id');
            $table->index(['user_id', 'is_active']);
        });
        
        echo "    ✓ Created workout_splits table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('workout_splits');
        echo "    ✓ Dropped workout_splits table\n";
    }
}
