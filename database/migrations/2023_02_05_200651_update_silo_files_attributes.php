<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSiloFilesAttributes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('silo_file_attributes', function (Blueprint $table) {
            $table->dropForeign('silo_file_attributes_silo_file_id_foreign');

            $table->string('parent_type')->after('silo_file_id')->nullable();
            $table->renameColumn('silo_file_id', 'parent_id');
        });

        DB::table('silo_file_attributes')->update([
            'parent_type' => \App\Models\SiloFile::class,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('silo_file_attributes', function (Blueprint $table) {
            $table->renameColumn('parent_id', 'silo_file_id');
            $table->foreign('silo_file_id')->references('id')->on('silo_files');

            $table->dropColumn(['parent_type']);

        });
    }
}
