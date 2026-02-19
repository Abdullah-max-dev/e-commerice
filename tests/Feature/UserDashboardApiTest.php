<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserDashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_fetch_dashboard_summary_and_paginated_orders(): void
    {
        $user = User::factory()->create([
            'verification_status' => 'verified',
            'billing_address' => 'Demo Address',
            'rewards_points' => 400,
        ]);

        Order::factory()->count(8)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $summaryResponse = $this->getJson('/api/dashboard/summary');
        $summaryResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.stats.total_orders', 8);

        $ordersResponse = $this->getJson('/api/dashboard/orders?per_page=5');
        $ordersResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.per_page', 5)
            ->assertJsonCount(5, 'data.data');
    }

    public function test_user_can_update_billing_address(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson('/api/dashboard/billing-address', [
            'billing_address' => '221B Baker Street, London NW1',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.billing_address', '221B Baker Street, London NW1');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'billing_address' => '221B Baker Street, London NW1',
        ]);
    }
}
