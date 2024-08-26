<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_mappings', function (Blueprint $table) {
            $table->increments('menu_mapping_id');         
            $table->integer('menu_group_id');
            $table->integer('menu_id');          
            $table->boolean('is_active')->default(true);;
            $table->integer('added_by');
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
        Schema::dropIfExists('menu_mappings');
    }
}
