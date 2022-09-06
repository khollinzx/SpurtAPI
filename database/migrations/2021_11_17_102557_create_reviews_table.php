<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("where");
            $table->string("product_quantity");
            $table->string("product_packaging");
            $table->string("shelf_life");
            $table->string("shipping");
            $table->string("customer_service");
            $table->string("content");
            $table->string("general_review");
            $table->string("made_in_score");
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('product_type_id')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL');
            $table->foreign('product_type_id')->references('id')->on('product_types')->onDelete('SET NULL');
            $table->foreign('creator_id')->references('id')->on('admins')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
