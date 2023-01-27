<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiloFileAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('silo_file_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('silo_file_id')->nullable();
            $table->string("type");
            $table->string("name");
            $table->json("attributes");
            $table->timestamps();

            $table->foreign('silo_file_id')
                ->references('id')
                ->on('silo_files')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('silo_file_attributes');
    }
}
