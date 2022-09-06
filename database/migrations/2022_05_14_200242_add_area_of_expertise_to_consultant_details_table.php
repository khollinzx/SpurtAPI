<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaOfExpertiseToConsultantDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consultant_details', function (Blueprint $table) {
            $table->text("area_of_expertises")->nullable();
            $table->unsignedBigInteger("currency_id")->nullable();

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete("SET NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consultant_details', function (Blueprint $table) {
            $table->dropForeign('consultant_details_currency_id_foreign');
            $table->dropColumn(['currency_id', 'area_of_expertises']);
        });
    }
}
