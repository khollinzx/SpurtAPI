<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminMenuBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_menu_bars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('menu_bar_id');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete("cascade");
            $table->foreign('menu_bar_id')->references('id')->on('menu_bars')->onDelete("cascade");
        });

        (new \App\Models\Admin())->setSuperAdminMenus();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_menu_bars');
    }
}
