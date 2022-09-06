<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('bill_to')->nullable();
            $table->string('reference_id');
            $table->string('po_number');
            $table->string('note');
            $table->string('description');
            $table->string('date');
            $table->string('due_date');
            $table->string('month');
            $table->string('year');
            $table->unsignedBigInteger('payment_type_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->double('amount_paid')->default(0);
            $table->double('balance_due')->default(0);
            $table->double('sub_total');
            $table->double('total');
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator_id')->references('id')->on('admins')->onDelete('SET NULL');
            $table->foreign('payment_type_id')->references('id')->on('payment_types')->onDelete('SET NULL');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('SET NULL');
            $table->foreign('bill_to')->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('company_id')->references('id')->on('product_types')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
