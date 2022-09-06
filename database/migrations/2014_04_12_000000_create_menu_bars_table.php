<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_bars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('link');
            $table->timestamps();
        });

        (new \App\Models\MenuBar())->initMenu();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_bars');
    }
}
