<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateFoodTrackingOnly
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Cache Nutritionix API responses to reduce external calls (7-day cache)
        if (!Capsule::schema()->hasTable('food_cache')) {
            Capsule::schema()->create('food_cache', function (Blueprint $table) {
                $table->id();
                $table->string('query', 255)->unique();
                $table->string('food_name', 255)->nullable();
                $table->integer('calories')->nullable();
                $table->decimal('protein_g', 6, 2)->nullable();
                $table->decimal('carbs_g', 6, 2)->nullable();
                $table->decimal('fat_g', 6, 2)->nullable();
                $table->string('serving_size', 100)->nullable();
                $table->timestamp('cached_at')->useCurrent();
                
                $table->index('query');
                $table->index('cached_at');
            });
            
            echo "    [OK] Created food_cache table\n";
        } else {
            echo "    [SKIP] food_cache table already exists, skipping\n";
        }

        // User meal logs
        if (!Capsule::schema()->hasTable('user_meals')) {
            Capsule::schema()->create('user_meals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('food_name', 255);
                $table->integer('calories');
                $table->decimal('protein_g', 6, 2)->default(0);
                $table->decimal('carbs_g', 6, 2)->default(0);
                $table->decimal('fat_g', 6, 2)->default(0);
                $table->string('serving_size', 100)->nullable();
                $table->string('meal_type', 50); // breakfast, lunch, dinner, snack
                $table->timestamp('logged_at')->useCurrent();
                
                // Foreign key
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                
                // Indexes
                $table->index(['user_id', 'logged_at']);
            });
            
            echo "    [OK] Created user_meals table\n";
        } else {
            echo "    [SKIP] user_meals table already exists, skipping\n";
        }

        // User daily nutrition summary (for quick dashboard stats)
        if (!Capsule::schema()->hasTable('user_daily_summary')) {
            Capsule::schema()->create('user_daily_summary', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('summary_date');
                $table->integer('calories_consumed')->default(0);
                $table->decimal('protein_g', 6, 2)->default(0);
                $table->decimal('carbs_g', 6, 2)->default(0);
                $table->decimal('fat_g', 6, 2)->default(0);
                $table->integer('meals_count')->default(0);
                $table->timestamp('updated_at')->useCurrent();
                
                // Foreign key
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                
                // Unique constraint
                $table->unique(['user_id', 'summary_date']);
                
                // Indexes
                $table->index(['user_id', 'summary_date']);
            });
            
            echo "    [OK] Created user_daily_summary table\n";
        } else {
            echo "    [SKIP] user_daily_summary table already exists, skipping\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('user_daily_summary');
        echo "    [OK] Dropped user_daily_summary table\n";
        
        Capsule::schema()->dropIfExists('user_meals');
        echo "    [OK] Dropped user_meals table\n";
        
        Capsule::schema()->dropIfExists('food_cache');
        echo "    [OK] Dropped food_cache table\n";
    }
}

