<?php

namespace Database\Factories;

use App\Models\DailyMenu;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyMenuFactory extends Factory
{
    protected $model = DailyMenu::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'menu_date' => fake()->dateTimeBetween('-3 days', '+3 days'),
            'title' => 'Daily Menu',
            'is_published' => fake()->boolean(),
        ];
    }
}
