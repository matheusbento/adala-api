<?php

namespace Database\Seeders;

use App\Models\Cube;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class CubeSeeder extends Seeder
{
    const NUMBER_OF_CUBES = 5;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Organization $organization)
    {
        echo '> Creating ' . self::NUMBER_OF_CUBES . " Cubes...\n";
        
        $cubes = Cube::factory()->count(self::NUMBER_OF_CUBES)->create(['organization_id' => $organization->id]);

        echo "Done\n\n";
    }
}
