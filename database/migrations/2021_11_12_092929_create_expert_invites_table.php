<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpertInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expert_invites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultant_id')->nullable();
            $table->unsignedBigInteger('assigner_id')->nullable();
            $table->morphs('invitable');
            $table->unsignedBigInteger('status_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('consultant_id')->references('id')->on('consultants')->onDelete('SET NULL');
            $table->foreign('assigner_id')->references('id')->on('admins')->onDelete('SET NULL');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('SET NULL');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expert_invites');
    }
}
