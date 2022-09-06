<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PaymentProvider;

class CreatePaymentProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('class');
            $table->tinyInteger('enabled');
            $table->timestamps();
        });

        (new PaymentProvider())::createAllProviders();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_providers');
    }
}
