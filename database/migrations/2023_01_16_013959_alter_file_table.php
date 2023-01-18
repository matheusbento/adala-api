<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('files', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();
            $table->dropForeign('files_user_id_foreign');
            $table->renameColumn('user_id', 'created_by_user_id');
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
            $table->softDeletes();
            Schema::enableForeignKeyConstraints();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            Schema::disableForeignKeyConstraints();
            $table->dropForeign('files_created_by_user_id_foreign');
            $table->renameColumn('created_by_user_id', 'user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->dropSoftDeletes();
            Schema::enableForeignKeyConstraints();
        });
    }
}
