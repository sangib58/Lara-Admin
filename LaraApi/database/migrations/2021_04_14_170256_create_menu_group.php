<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_groups', function (Blueprint $table) {
            $table->increments('menu_group_id');         
            $table->string('menu_group_name')->unique();
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
        Schema::dropIfExists('menu_groups');

    }
}
