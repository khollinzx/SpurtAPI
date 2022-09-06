<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTalentPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('talent_pools', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email");
            $table->string("address");
            $table->string("phone");
            $table->string("alt_phone");
            $table->string("linkedin_profile");
            $table->string("cv");
            $table->string("profession");
            $table->string("what_you_do");
            $table->text("contributions");//[{ name: Professional Translation & Interpretation}]//change to json later
            $table->string("previous_project");
            $table->enum("coordinate_answer", ['Yes','No','Maybe']);
            $table->enum("mentor_answer", ['Yes','No','Maybe']);
            $table->string('other_payment_address')->nullable();
            $table->double('agreed_amount')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_code')->nullable();
            $table->unsignedBigInteger('consultant_id')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->unsignedBigInteger("platform_type_id")->nullable();
            $table->unsignedBigInteger("country_id")->nullable();
            $table->tinyInteger("is_verified")->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('consultant_id')->references('id')->on('consultants')->onDelete("cascade");
            $table->foreign('platform_type_id')->references('id')->on('platform_types')->onDelete("SET NULL");
            $table->foreign('country_id')->references('id')->on('countries')->onDelete("SET NULL");
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete("SET NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('talent_pools');
    }
}
