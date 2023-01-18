<?php

namespace Database\Seeders;

use App\Models\Cube;
use App\Models\CubeMetadata;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CubeSeeder extends Seeder
{
    public const NUMBER_OF_CUBES = 5;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Organization $organization)
    {
        $metadatas = array(
            [
                'field' => 'start_date',
                'value' => Carbon::now(),
            ],
            [
                'field' => 'end_date',
                'value' => Carbon::now()->addMonth(1),
            ],
        );
        
        echo '> Creating ' . self::NUMBER_OF_CUBES . " Cubes...\n";

        $cubes = Cube::factory()->count(self::NUMBER_OF_CUBES)->create(['organization_id' => $organization->id]);

        foreach ($cubes as $cube) {
            foreach ($metadatas as $meta) {
                CubeMetadata::factory()->create(array_merge($meta, ['cube_id' => $cube->id]));
            }
        }

        echo "Done\n\n";
    }
}
