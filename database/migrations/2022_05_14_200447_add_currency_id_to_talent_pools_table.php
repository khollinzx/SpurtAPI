<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyIdToTalentPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('talent_pools', function (Blueprint $table) {
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
        Schema::table('talent_pools', function (Blueprint $table) {
            $table->dropForeign('talent_pools_currency_id_foreign');
            $table->dropColumn(['currency_id']);
        });
    }
}
