<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->increments('user_role_id');
            $table->string('role_name')->unique();
            $table->text('role_desc')->nullable();
            $table->integer('menu_group_id')->nullable();
            $table->boolean('is_active')->default(true);;
            $table->integer('added_by');
            $table->integer('last_updated_by')->nullable();
            $table->boolean('is_migration_data')->default(true);;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {     
        Schema::dropIfExists('user_roles');     
    }
}
