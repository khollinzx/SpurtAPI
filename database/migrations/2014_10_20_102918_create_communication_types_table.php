<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CommunicationType;

class CreateCommunicationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator_id')->references('id')->on('admins')->onDelete('SET NULL');
        });

        (new CommunicationType())::initCommunicationType();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('communication_types');
    }
}
