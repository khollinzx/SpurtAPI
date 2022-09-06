<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_consultations', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email");
            $table->string("company_name");
            $table->string("phone");
            $table->string("address");
            $table->text("communication_type")->nullable();
            $table->string("about_business");
            $table->string("achievement");
            $table->string("expectation");
            $table->string("goals");
            $table->string("constraints");
            $table->string("outcomes");
            $table->string("target");
            $table->text('areas_of_need')->nullable();
            $table->double("budget");
            $table->string("timeline");
            $table->string("questions");
            $table->timestamps();
            $table->softDeletes();

//            $table->foreign('communication_type_id')->references('id')->on('communication_types')->onDelete("SET NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pre_consultantions');
    }
}
