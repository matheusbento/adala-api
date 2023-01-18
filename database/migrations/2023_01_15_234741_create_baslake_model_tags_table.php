<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaslakeModelTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baslake_model_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('baslake_tag_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->foreign('baslake_tag_id')->references('id')->on('baslake_tags')->onDelete('cascade');

            $table->unique(['baslake_tag_id', 'model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baslake_model_tags');
    }
}
