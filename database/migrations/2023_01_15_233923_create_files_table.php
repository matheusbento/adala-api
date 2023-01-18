<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('owner_type');
            $table->integer('owner_id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('file_type')->index();
            $table->string('drive');
            $table->string('url');
            $table->string('path');
            $table->string('original');
            $table->string('mime');
            $table->unsignedInteger('size');
            $table->integer('sort_order')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['owner_type', 'owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
