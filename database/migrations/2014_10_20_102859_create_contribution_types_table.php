<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ContributionType;

class CreateContributionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contribution_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator_id')->references('id')->on('admins')->onDelete('SET NULL');
        });

        (new ContributionType())::initContributionType();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contribution_types');
    }
}
