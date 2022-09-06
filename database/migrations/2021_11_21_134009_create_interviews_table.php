<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('media_link');
            $table->string('image');
            $table->text('interview_text');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('interview_type_id')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('SET NULL');
            $table->foreign('interview_type_id')->references('id')->on('interview_types')->onDelete('SET NULL');
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
        Schema::dropIfExists('interviews');
    }
}
