<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangePreviousProjectAndWhatYouDoFieldDataType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('talent_pools', function (Blueprint $table) {
            $table->longText('what_you_do')->change();
            $table->longText('previous_project')->change();
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
            $table->string('what_you_do')->change();
            $table->string('previous_project')->change();
        });
    }
}
