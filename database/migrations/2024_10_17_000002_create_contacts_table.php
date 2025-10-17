<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateContactsTable
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Capsule::schema()->create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100);
            $table->string('subject', 200);
            $table->text('message');
            $table->string('status', 20)->default('unread');
            $table->timestamps();
            
            $table->index('status');
            $table->index('created_at');
        });
        
        echo "    ✓ Created contacts table\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('contacts');
        echo "    ✓ Dropped contacts table\n";
    }
}
