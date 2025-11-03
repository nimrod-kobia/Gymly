<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAchievementsTable
{
    /**
     * Run the migrations.
     * 
     * Track which achievements users have unlocked
     */
    public function up()
    {
        Capsule::schema()->create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('achievement_id');
            $table->timestamp('unlocked_at')->useCurrent();
            $table->boolean('is_viewed')->default(false); // Show notification badge
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('achievement_id')->references('id')->on('achievements')->onDelete('cascade');
            
            // Indexes
            $table->index('user_id');
            $table->index(['user_id', 'is_viewed']);
            $table->unique(['user_id', 'achievement_id']); // Can't unlock same achievement twice
        });
        
        echo "    ✓ Created user_achievements table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('user_achievements');
        echo "    ✓ Dropped user_achievements table\n";
    }
}
