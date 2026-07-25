<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => Str::slug(fake()->unique()->company()),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'pix_key' => fake()->uuid(),
            'whatsapp_number' => fake()->phoneNumber(),
            'delivery_fee' => fake()->randomFloat(2, 0, 15),
            'minimum_order' => fake()->randomFloat(2, 10, 30),
            'opening_hours' => [
                'monday' => ['open' => '08:00', 'close' => '22:00'],
                'tuesday' => ['open' => '08:00', 'close' => '22:00'],
                'wednesday' => ['open' => '08:00', 'close' => '22:00'],
                'thursday' => ['open' => '08:00', 'close' => '22:00'],
                'friday' => ['open' => '08:00', 'close' => '23:00'],
                'saturday' => ['open' => '09:00', 'close' => '23:00'],
                'sunday' => ['open' => '10:00', 'close' => '20:00'],
            ],
            'is_active' => true,
            'plan_id' => \App\Models\Plan::where('slug', 'pro')->first()?->id,
            'subscription_status' => 'active',
            'paid_until' => now()->addMonth(),
        ];
    }
}
