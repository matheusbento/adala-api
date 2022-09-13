<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddOrganizationPermissions extends Migration
{
    public const PERMISSIONS_TO_ADD = [
        'baslake_organizations_access',
        'baslake_organizations_manage',
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permissions')->insertOrIgnore(
            array_map(
                fn ($name) => [
                    'name' => $name,
                    'guard_name' => 'api',
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()'),
                ],
                self::PERMISSIONS_TO_ADD
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
        DB::table('permissions')->whereIn('name', self::PERMISSIONS_TO_ADD)->delete();
    }
}
