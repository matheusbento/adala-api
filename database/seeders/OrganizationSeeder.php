<?php

namespace Database\Seeders;

use App\Constants\Permissions;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user)
    {
        $organization = Organization::factory()->create(['owner_id' => $user->id]);

        echo '> Seeding new Organization: ' . $organization->name . "\n";

        $user->givePermissionTo(
            [
                Permissions::BASLAKE_ACCESS,
                Permissions::BASLAKE_CUBES_ACCESS,
                Permissions::BASLAKE_CUBES_MANAGE,
                Permissions::BASLAKE_DATASETS_ACCESS,
                Permissions::BASLAKE_DATASETS_MANAGE,
                Permissions::BASLAKE_ORGANIZATIONS_ACCESS,
                Permissions::BASLAKE_ORGANIZATIONS_MANAGE
            ],
            $organization
        );

        echo '> Giving Permissions to User: ' . $user->email . " access Organization: " . $organization->name . "\n";

        $this->call(CubeSeeder::class, false, [
            'organization' => $organization,
        ]);
    }
}
