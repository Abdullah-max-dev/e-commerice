<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:25',
            'address' => 'required|string|max:1000',
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer,card',
        ]);

        $user = $request->user();

        $cartItems = CartItem::with(['product.discount', 'product.mainImage'])
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.',
            ], 422);
        }

        [$items, $subtotal, $total] = $this->prepareOrderItems($cartItems);

        DB::transaction(function () use ($user, $validated, $items, $subtotal, $total) {
            Order::create([
                'user_id' => $user->id,
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'],
                'customer_address' => $validated['address'],
                'payment_method' => $validated['payment_method'],
                'subtotal' => round($subtotal, 2),
                'discount' => round($subtotal - $total, 2),
                'total' => round($total, 2),
                'items' => $items,
                'status' => 'pending',
            ]);

            CartItem::where('user_id', $user->id)->delete();
        });

        return response()->json([
            'message' => 'Order placed successfully.',
            'cart_items' => [],
            'summary' => [
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
                'items_count' => 0,
            ],
        ], 201);
    }

    private function prepareOrderItems($cartItems)
    {
        $subtotal = 0;
        $total = 0;

        $items = $cartItems->map(function ($item) use (&$subtotal, &$total) {
            $product = $item->product;

            $basePrice = (float) $product->p_price;
            $finalPrice = (float) $product->final_price;
            $lineSubtotal = $basePrice * $item->quantity;
            $lineTotal = $finalPrice * $item->quantity;

            $subtotal += $lineSubtotal;
            $total += $lineTotal;

            return [
                'p_id' => $product->p_id,
                'name' => $product->p_name,
                'price' => round($finalPrice, 2),
                'quantity' => (int) $item->quantity,
                'line_total' => round($lineTotal, 2),
            ];
        })->values();

        return [$items, $subtotal, $total];
    }
}
