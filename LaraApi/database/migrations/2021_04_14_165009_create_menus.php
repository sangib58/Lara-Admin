<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('menu_id');
            $table->integer('parent_id');
            $table->string('menu_title')->unique();
            $table->string('url')->nullable();
            $table->integer('is_sub_menu');
            $table->integer('sort_order');
            $table->string('icon_class')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('menus');
    }
}
