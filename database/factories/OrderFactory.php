<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'customer_id' => Customer::factory(),
            'order_number' => 'ORD-' . now()->format('Ymd') . '-' . fake()->unique()->numerify('####'),
            'status' => fake()->randomElement(Order::STATUSES),
            'source' => 'whatsapp',
            'subtotal' => fake()->randomFloat(2, 20, 100),
            'delivery_fee' => fake()->randomFloat(2, 0, 10),
            'discount' => 0,
            'total' => 0,
            'delivery_type' => fake()->randomElement(['delivery', 'pickup']),
            'delivery_address' => fake()->address(),
            'ordered_at' => fake()->dateTimeBetween('-7 days'),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {
            $order->update(['total' => $order->subtotal + $order->delivery_fee - $order->discount]);
        });
    }
}
