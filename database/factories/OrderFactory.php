<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 40, 700);
        $discount = fake()->randomFloat(2, 0, 50);
        $total = max($subtotal - $discount, 1);

        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . fake()->unique()->numerify('######'),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->numerify('+1##########'),
            'customer_address' => fake()->address(),
            'payment_method' => fake()->randomElement(['card', 'paypal', 'bank_transfer']),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'items' => [
                [
                    'name' => fake()->words(2, true),
                    'quantity' => fake()->numberBetween(1, 4),
                    'unit_price' => fake()->randomFloat(2, 10, 200),
                ],
            ],
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
        ];
    }
}
