<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_services', function (Blueprint $table) {
            $table->id();
            $table->string('tag_no');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('date');
            $table->string('time');
            $table->string('month');
            $table->string('year');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('assigned_admin_id')->nullable();//Admin
            $table->unsignedBigInteger('platform_type_id')->nullable();//PaperClip
            $table->text('services');//{name: "Editing",name: "Proof-Reading"}//change to json later
            $table->unsignedBigInteger('is_assigned_id')->nullable();//Assigned, Unassigned
            $table->unsignedBigInteger('is_approved_id')->nullable();//Approved, Pending
            $table->unsignedBigInteger('payment_status_id')->nullable();//Paid,Unpaid
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL');
            $table->foreign('assigned_admin_id')->references('id')->on('admins')->onDelete('SET NULL');
            $table->foreign('platform_type_id')->references('id')->on('platform_types')->onDelete('SET NULL');
            $table->foreign('is_assigned_id')->references('id')->on('statuses')->onDelete('SET NULL');
            $table->foreign('is_approved_id')->references('id')->on('statuses')->onDelete('SET NULL');
            $table->foreign('payment_status_id')->references('id')->on('statuses')->onDelete('SET NULL');
//            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_services');
    }
}
