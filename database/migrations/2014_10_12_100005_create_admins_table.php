<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Admin;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('phone')->nullable();
//            $table->text('resources')->nullable();
//            $table->text('privileges')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('platform_type_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->tinyInteger('is_active')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('SET NULL');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL');
            $table->foreign('platform_type_id')->references('id')->on('platform_types')->onDelete('SET NULL');
            $table->foreign('creator_id')->references('id')->on('admins')->onDelete('SET NULL');
        });

        (new Admin())::initAdmin();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
