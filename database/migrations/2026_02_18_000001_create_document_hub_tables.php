<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentHubTables
{
    public function up()
    {
        if (!Capsule::schema()->hasTable('companies')) {
            Capsule::schema()->create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150)->unique();
                $table->text('description')->nullable();
                $table->string('website_url', 255)->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('name');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });

            echo "    [OK] Created companies table\n";
        } else {
            echo "    [SKIP] companies table already exists, skipping\n";
        }

        if (!Capsule::schema()->hasTable('company_employees')) {
            Capsule::schema()->create('company_employees', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('user_id');
                $table->string('position_title', 120)->nullable();
                $table->string('employment_status', 40)->default('active');
                $table->boolean('is_company_admin')->default(false);
                $table->timestamp('joined_at')->useCurrent();

                $table->unique(['company_id', 'user_id']);
                $table->index(['company_id', 'employment_status']);
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

            echo "    [OK] Created company_employees table\n";
        } else {
            echo "    [SKIP] company_employees table already exists, skipping\n";
        }

        if (!Capsule::schema()->hasTable('publications')) {
            Capsule::schema()->create('publications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('publisher_user_id');
                $table->unsignedBigInteger('company_id')->nullable();
                $table->string('title', 200);
                $table->text('summary')->nullable();
                $table->text('keywords')->nullable();
                $table->string('file_path', 255);
                $table->string('original_file_name', 255);
                $table->integer('file_size_bytes')->default(0);
                $table->integer('downloads_count')->default(0);
                $table->boolean('is_public')->default(true);
                $table->timestamp('published_at')->useCurrent();
                $table->timestamps();

                $table->index(['company_id', 'published_at']);
                $table->index(['publisher_user_id', 'published_at']);
                $table->index('title');
                $table->foreign('publisher_user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            });

            echo "    [OK] Created publications table\n";
        } else {
            echo "    [SKIP] publications table already exists, skipping\n";
        }
    }

    public function down()
    {
        Capsule::schema()->dropIfExists('publications');
        echo "    [OK] Dropped publications table\n";

        Capsule::schema()->dropIfExists('company_employees');
        echo "    [OK] Dropped company_employees table\n";

        Capsule::schema()->dropIfExists('companies');
        echo "    [OK] Dropped companies table\n";
    }
}

