<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCubesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cubes', function (Blueprint $table) {
            $table->string('current_status')->after('identifier')->nullable();
            $table->string('identifier')->nullable()->change();
            $table->dropColumn(['model']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cubes', function (Blueprint $table) {
            $table->longText('model')->nullable()->after('description');
            $table->string('identifier')->nullable(false)->change();
            $table->dropColumn(['current_status']);
        });
    }
}
