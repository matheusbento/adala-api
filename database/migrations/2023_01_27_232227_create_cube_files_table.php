<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCubeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cube_files', function (Blueprint $table) {
            $table->unsignedBigInteger('silo_file_id');
            $table->unsignedBigInteger('cube_id');

            $table->foreign('silo_file_id')->references('id')->on('silo_files');
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
        Schema::dropIfExists('cube_files');
    }
}
