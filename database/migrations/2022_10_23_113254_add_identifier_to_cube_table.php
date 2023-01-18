<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddIdentifierToCubeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $model = '{"dimensions":[{"name":"item","levels":[{"name":"category","label":"Category","attributes":["category","category_label"]},{"name":"subcategory","label":"Sub-category","attributes":["subcategory","subcategory_label"]},{"name":"line_item","label":"Line Item","attributes":["line_item"]}]},{"name":"year","role":"time"}],"cubes":[{"id":"uid","name":"irbd_balance","dimensions":["item","year"],"measures":[{"name":"amount","label":"Amount"}],"aggregates":[{"name":"amount_sum","function":"sum","measure":"amount"},{"name":"record_count","function":"count"}],"mappings":{"item.line_item":"line_item","item.subcategory":"subcategory","item.subcategory_label":"subcategory_label","item.category":"category","item.category_label":"category_label"},"info":{"id":"887d82fc-3eb9-458e-9240-4d9ca5656b3d","min_date":"2010-01-01","max_date":"2010-12-31"}}]}';

        Schema::table('cubes', function (Blueprint $table) {
            $table->longText('model')->nullable()->after('description');
            $table->string('identifier')->nullable()->after('name');

            $table->unique(['identifier']);
        });

        DB::table('cubes')->update([
            'model' => $model,
        ]);

        DB::table('cubes')->eachById(function ($cube){
            DB::table('cubes')->where('id', $cube->id)->update(
                [
                    'identifier' => Str::random(50),
                ]
            );
        });

        Schema::table('cubes', function (Blueprint $table) {
            $table->longText('model')->nullable(false)->change();
            $table->string('identifier')->nullable(false)->change();
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
            $table->dropUnique(['identifier']);
            $table->dropColumn(['model', 'identifier']);
        });
    }
}
