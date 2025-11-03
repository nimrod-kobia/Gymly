<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateWorkoutSessionsTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('split_id')->nullable();
            $table->unsignedBigInteger('split_day_id')->nullable();
            $table->date('workout_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->nullable(); // Calculated field
            $table->decimal('total_calories_burned', 8, 2)->nullable();
            $table->text('notes')->nullable(); // How they felt, energy level
            $table->enum('mood', ['great', 'good', 'okay', 'tired', 'poor'])->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('split_id')->references('id')->on('workout_splits')->onDelete('set null');
            $table->foreign('split_day_id')->references('id')->on('split_days')->onDelete('set null');
            
            // Indexes
            $table->index('user_id');
            $table->index('workout_date');
            $table->index(['user_id', 'workout_date']);
            $table->index(['user_id', 'is_completed']);
        });
        
        echo "    ✓ Created workout_sessions table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('workout_sessions');
        echo "    ✓ Dropped workout_sessions table\n";
    }
}
