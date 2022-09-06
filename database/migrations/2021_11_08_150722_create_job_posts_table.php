<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company_name');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger("creator_id")->nullable();
            $table->unsignedBigInteger("job_type_id")->nullable();
            $table->unsignedBigInteger("duration_type_id")->nullable();
            $table->text('descriptions');
            $table->text('responsibilities');
            $table->text('requirements');
            $table->text('summaries');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('duration_type_id')->references('id')->on('duration_types')->onDelete('SET NULL');
            $table->foreign('job_type_id')->references('id')->on('job_types')->onDelete('SET NULL');
            $table->foreign('creator_id')->references('id')->on('admins')->onDelete('SET NULL');
            $table->foreign('location_id')->references('id')->on('countries')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_posts');
    }
}
