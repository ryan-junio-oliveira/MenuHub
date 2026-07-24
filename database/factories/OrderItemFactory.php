<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'dish_name' => fake()->word(),
            'size' => fake()->randomElement(['small', 'medium', 'large']),
            'quantity' => fake()->numberBetween(1, 5),
            'unit_price' => fake()->randomFloat(2, 10, 50),
            'subtotal' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (OrderItem $item) {
            $item->update(['subtotal' => $item->unit_price * $item->quantity]);
        });
    }
}
