<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DailyMenu;
use App\Models\DailyMenuItem;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Plan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        User::create([
            'name' => env('ROOT_NAME', 'Root'),
            'email' => env('ROOT_EMAIL', 'root@menuhub.com'),
            'password' => Hash::make(env('ROOT_PASSWORD', 'password')),
            'role' => 'root',
            'restaurant_id' => null,
        ]);
    }
}
