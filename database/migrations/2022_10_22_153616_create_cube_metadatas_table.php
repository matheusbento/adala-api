<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCubeMetadatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cube_metadatas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cube_id');
            $table->string('field');
            $table->text('value');
            $table->timestamps();

            $table->foreign('cube_id')->references('id')->on('cubes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cube_metadatas');
    }
}
