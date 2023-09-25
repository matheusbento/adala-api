<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCubeDashboardItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cube_dashboard_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cube_id');
            $table->string('name');
            $table->string('chart');
            $table->string('processing_method')->nullable();
            $table->text('select');
            $table->text('filter');
            $table->text('layout');
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
        Schema::dropIfExists('cube_dashboard_item');
    }
}
