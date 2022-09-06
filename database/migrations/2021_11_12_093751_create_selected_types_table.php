<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SelectedType;

class CreateSelectedTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selected_types', function (Blueprint $table) {
            $table->id();
            $table->morphs('selectable');
            $table->timestamps();
        });

        (new SelectedType())::runSelectables();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selected_types');
    }
}
