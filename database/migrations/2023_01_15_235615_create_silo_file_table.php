<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiloFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('silo_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('folder_id')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
            $table->datetime('file_uploaded_at')->nullable();

            $table->foreign('folder_id')->references('id')->on('silo_folders');
            $table->foreign('file_id')
                ->references('id')
                ->on('files')
                ->onDelete('SET NULL');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('silo_files');
    }
}
