<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VendorDashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_notifications_endpoint_handles_missing_notifications_table(): void
    {
        $vendor = User::factory()->create([
            'role' => 'vender',
            'verification_status' => 'unverified',
        ]);

        Schema::dropIfExists('vendor_notifications');

        Sanctum::actingAs($vendor);

        $response = $this->getJson('/api/vender/dashboard/notifications');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Notifications fetched successfully.')
            ->assertJsonPath('data.0.type', 'verification');
    }
}
