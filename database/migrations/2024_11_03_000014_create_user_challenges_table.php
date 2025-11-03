<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateUserChallengesTable
{
    /**
     * Run the migrations.
     * 
     * Track user participation in challenges
     */
    public function up()
    {
        Capsule::schema()->create('user_challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('challenge_id');
            $table->decimal('current_progress', 10, 2)->default(0); // Current value achieved
            $table->decimal('progress_percentage', 5, 2)->default(0); // % completed
            $table->enum('status', ['active', 'completed', 'failed', 'abandoned'])->default('active');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            
            // Indexes
            $table->index('user_id');
            $table->index(['user_id', 'status']);
            $table->unique(['user_id', 'challenge_id']); // Can't join same challenge twice
        });
        
        echo "    ✓ Created user_challenges table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('user_challenges');
        echo "    ✓ Dropped user_challenges table\n";
    }
}
