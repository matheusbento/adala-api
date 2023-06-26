<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCubeFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cube_folders', function (Blueprint $table) {
            $table->unsignedBigInteger('cube_id');
            $table->unsignedBigInteger('silo_folder_id');

            $table->foreign('cube_id')->references('id')->on('cubes');
            $table->foreign('silo_folder_id')->references('id')->on('silo_folders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cube_folders');
    }
}
