<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTable extends Migration
{
    public const CATEGORIES = [
        ['name' => 'High Energy', 'description' => ''],
        ['name' => 'Radio', 'description' => ''],
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('categories')->insertOrIgnore(
            array_map(
                fn ($data) => $data,
                self::CATEGORIES
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
