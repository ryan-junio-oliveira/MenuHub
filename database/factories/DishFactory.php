<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DishFactory extends Factory
{
    protected $model = Dish::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'dish_category_id' => DishCategory::factory(),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'price_small' => fake()->randomFloat(2, 10, 25),
            'price_medium' => fake()->randomFloat(2, 15, 35),
            'price_large' => fake()->randomFloat(2, 20, 45),
            'is_gourmet' => fake()->boolean(20),
            'max_selections' => 1,
            'is_available' => true,
            'is_active' => true,
        ];
    }
}
