<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFolderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('silo_folders', function (Blueprint $table) {
            $table->boolean('is_dataflow')->default(0)->after('description');
            $table->unsignedBigInteger('category_id')->nullable()->after('description');

            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::table('cubes', function (Blueprint $table) {
            $table->boolean('is_dataflow')->default(0)->after('description');
            $table->unsignedBigInteger('category_id')->nullable()->after('description');

            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('silo_folders', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['is_dataflow', 'category_id']);
        });

        Schema::table('cubes', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['is_dataflow', 'category_id']);
        });
    }
}
