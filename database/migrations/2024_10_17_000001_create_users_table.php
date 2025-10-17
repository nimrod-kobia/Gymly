<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Capsule::schema()->hasTable('users')) {
            Capsule::schema()->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('full_name', 100);
                $table->string('username', 50)->unique();
                $table->string('email', 100)->unique();
                $table->string('password_hash', 255);
                $table->boolean('is_verified')->default(false);
                $table->string('verification_code', 100)->nullable();
                $table->timestamp('code_expiry')->nullable();
                $table->timestamps();
                
                $table->index('email');
                $table->index('username');
            });
            
            echo "    ✓ Created users table\n";
        } else {
            echo "    ⏭️  Users table already exists, skipping\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('users');
        echo "    ✓ Dropped users table\n";
    }
}
