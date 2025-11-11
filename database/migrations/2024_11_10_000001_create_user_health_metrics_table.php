<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateUserHealthMetricsTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Capsule::schema()->hasTable('user_health_metrics')) {
            Capsule::schema()->create('user_health_metrics', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                
                // Body Measurements
                $table->decimal('weight_kg', 5, 2)->nullable();
                $table->decimal('height_cm', 5, 2)->nullable();
                $table->decimal('body_fat_percentage', 4, 2)->nullable();
                $table->decimal('muscle_mass_kg', 5, 2)->nullable();
                $table->decimal('bmi', 4, 2)->nullable();
                
                // Vital Signs
                $table->integer('resting_heart_rate')->nullable();
                $table->integer('blood_pressure_systolic')->nullable();
                $table->integer('blood_pressure_diastolic')->nullable();
                
                // Lifestyle
                $table->decimal('hours_slept', 3, 1)->nullable();
                $table->integer('water_intake_ml')->nullable();
                
                // Additional Info
                $table->text('notes')->nullable();
                
                $table->timestamp('recorded_at')->useCurrent();
                $table->timestamps();
                
                // Foreign key
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                
                // Indexes
                $table->index('user_id');
                $table->index('recorded_at');
                $table->index(['user_id', 'recorded_at']);
            });
            
            echo "    ✓ Created user_health_metrics table\n";
        } else {
            echo "    ⏭️  User health metrics table already exists, skipping\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('user_health_metrics');
        echo "    ✓ Dropped user_health_metrics table\n";
    }
}
