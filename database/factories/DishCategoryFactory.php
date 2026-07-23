<?php

namespace Database\Factories;

use App\Models\DishCategory;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DishCategoryFactory extends Factory
{
    protected $model = DishCategory::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => fake()->word(),
            'display_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
