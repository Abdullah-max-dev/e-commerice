<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
            'verification_status' => 'verified',
            'billing_address' => '74 Market Street, Suite 201, Dhaka, Bangladesh',
            'rewards_points' => 1280,
        ]);

        Order::factory()->count(18)->create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '+8801700000000',
            'customer_address' => $user->billing_address,
            'payment_method' => 'card',
        ]);
    }
}
