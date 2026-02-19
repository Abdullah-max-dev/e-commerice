<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();

        $ordersQuery = Order::query()->where('user_id', $user->id);
        $totalOrders = (clone $ordersQuery)->count();
        $totalSpent = (float) (clone $ordersQuery)->sum('total');

        return response()->json([
            'success' => true,
            'message' => 'Dashboard summary fetched successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'verification_status' => $user->verification_status,
                    'billing_address' => $user->billing_address,
                    'rewards_points' => (int) $user->rewards_points,
                    'avatar_url' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0F172A&color=fff',
                ],
                'stats' => [
                    'total_orders' => $totalOrders,
                    'total_spent' => round($totalSpent, 2),
                    'account_verified' => $user->verification_status === 'verified',
                    'rewards_points' => (int) $user->rewards_points,
                ],
            ],
        ]);
    }

    public function recentOrders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_page' => 'nullable|integer|min:5|max:25',
        ]);

        $perPage = $validated['per_page'] ?? 10;

        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($perPage)
            ->through(fn (Order $order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'date' => optional($order->created_at)->toDateString(),
                'total' => (float) $order->total,
                'status' => $order->status,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Recent orders fetched successfully.',
            'data' => $orders,
        ]);
    }

    public function updateBillingAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'billing_address' => 'required|string|min:8|max:500',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Billing address updated successfully.',
            'data' => [
                'billing_address' => $user->billing_address,
            ],
        ]);
    }
}
