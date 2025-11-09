<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class CreateExerciseCompletionsTable
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('exercise_completions')) {
            Capsule::schema()->create('exercise_completions', function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('split_day_id');
                $table->unsignedBigInteger('exercise_id');
                $table->date('completion_date');
                $table->boolean('completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('split_day_id')->references('id')->on('split_days')->onDelete('cascade');
                $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');

                // Unique constraint to prevent duplicate completions
                $table->unique(['user_id', 'split_day_id', 'exercise_id', 'completion_date'], 'unique_completion');
                
                // Index for faster queries
                $table->index(['user_id', 'completion_date']);
            });
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('exercise_completions');
    }
}
