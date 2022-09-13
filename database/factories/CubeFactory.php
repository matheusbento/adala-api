<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class CubeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return ['name' => $this->faker->word, 'description' => $this->faker->sentence, 'organization_id' => Organization::factory()];
    }
}
