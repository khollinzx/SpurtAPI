<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsVerifiedToPreConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pre_consultations', function (Blueprint $table) {
            $table->tinyInteger("is_verified")->default(0)->after("questions");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pre_consultations', function (Blueprint $table) {
            $table->dropColumn(["is_verified"]);
        });
    }
}
