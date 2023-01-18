<?php

namespace Database\Factories;

use App\Models\Cube;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CubeMetadataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return ['field' => 'start_date', 'value' => Carbon::now(), 'cube_id' => Cube::factory()];
    }
}
