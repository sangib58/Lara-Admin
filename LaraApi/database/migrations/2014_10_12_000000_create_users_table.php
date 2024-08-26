<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_role_id');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('mobile')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('image_path')->nullable();
            $table->rememberToken();
            $table->boolean('is_active')->default(true);
            $table->integer('added_by');
            $table->integer('last_updated_by')->nullable();
            $table->boolean('is_migration_data')->default(true);
            $table->timestamps();
            /* $table->increments('UserId');
            $table->integer('UserRoleId');
            $table->string('FullName');
            $table->string('Mobile')->nullable();
            $table->string('Email')->nullable();
            $table->date('DateOfBirth')->nullable();
            $table->text('ImagePath')->nullable();
            $table->string('UserName');
            $table->string('Password');
            $table->boolean('IsActive');
            $table->integer('AddedBy');
            $table->integer('LastUpdatedBy')->nullable();
            $table->boolean('IsMigrationData');
            $table->timestamps(); */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
