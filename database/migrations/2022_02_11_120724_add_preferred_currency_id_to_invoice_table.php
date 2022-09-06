<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Invoice;

class AddPreferredCurrencyIdToInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('preferred_currency_id')->nullable();
            $table->foreign('preferred_currency_id')->references('id')->on('currencies')->onDelete('SET NULL');
        });

        (new Invoice())::setDefaultPreferredCurrencyToNaira();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_preferred_currency_id_foreign');
            $table->dropColumn(['preferred_currency_id']);
        });
    }
}
