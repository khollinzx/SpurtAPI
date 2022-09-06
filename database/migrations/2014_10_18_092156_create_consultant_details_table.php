<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultantDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultant_details', function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable();
            $table->string('business_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('other_payment_address')->nullable();
            $table->double('agreed_amount')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_code')->nullable();
            $table->unsignedBigInteger('consultant_id');
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('consultant_id')->references('id')->on('consultants')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultant_details');
    }
}
