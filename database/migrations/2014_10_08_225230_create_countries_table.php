<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Country;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->integer('code')->nullable();
            $table->integer('digit_length')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('SET NULL');
        });

        (new Country())::initCountry();
        (new Country())::runSQL();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
