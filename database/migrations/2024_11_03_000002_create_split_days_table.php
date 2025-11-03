<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateSplitDaysTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('split_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('split_id');
            $table->string('day_name', 100); // e.g., "Push Day", "Chest & Triceps", "Rest Day"
            $table->tinyInteger('day_of_week')->nullable(); // 1=Monday, 7=Sunday, NULL=flexible
            $table->boolean('is_rest_day')->default(false);
            $table->text('notes')->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            // Foreign key
            $table->foreign('split_id')->references('id')->on('workout_splits')->onDelete('cascade');
            
            // Indexes
            $table->index('split_id');
            $table->index(['split_id', 'day_of_week']);
        });
        
        echo "    ✓ Created split_days table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('split_days');
        echo "    ✓ Dropped split_days table\n";
    }
}
