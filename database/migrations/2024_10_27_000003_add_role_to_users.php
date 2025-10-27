<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class AddRoleToUsers
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
        });
        
        echo "    ✓ Added role column to users table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        echo "    ✓ Dropped role column from users table\n";
    }
}
