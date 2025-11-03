<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class AllowNullUserIdInWorkoutSplits
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Make user_id nullable for preset splits
        Capsule::schema()->table('workout_splits', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
        
        echo "    ✓ Made user_id nullable in workout_splits table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->table('workout_splits', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
        
        echo "    ✓ Reverted user_id to not nullable in workout_splits table\n";
    }
}
