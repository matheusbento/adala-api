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
        $model = '{"dimensions":[{"name":"item","levels":[{"name":"category","label":"Category","attributes":["category","category_label"]},{"name":"subcategory","label":"Sub-category","attributes":["subcategory","subcategory_label"]},{"name":"line_item","label":"Line Item","attributes":["line_item"]}]},{"name":"year","role":"time"}],"cubes":[{"id":"uid","name":"irbd_balance","dimensions":["item","year"],"measures":[{"name":"amount","label":"Amount"}],"aggregates":[{"name":"amount_sum","function":"sum","measure":"amount"},{"name":"record_count","function":"count"}],"mappings":{"item.line_item":"line_item","item.subcategory":"subcategory","item.subcategory_label":"subcategory_label","item.category":"category","item.category_label":"category_label"},"info":{"id":"887d82fc-3eb9-458e-9240-4d9ca5656b3d","min_date":"2010-01-01","max_date":"2010-12-31"}}]}';

        return ['name' => $this->faker->word, 'description' => $this->faker->sentence, 'organization_id' => Organization::factory(), 'model' => json_decode($model, true)];
    }
}
