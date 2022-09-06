<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNominatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nominates', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email");
            $table->string("product_name");
            $table->string("where_link");
            $table->string("image");
            $table->string("contact_name");
            $table->string("contact_email");
            $table->string("contact_phone");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nominates');
    }
}
