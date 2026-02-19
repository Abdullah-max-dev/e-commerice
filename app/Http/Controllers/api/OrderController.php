<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private const USER_ALLOWED_STATUS = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    private const VENDER_ALLOWED_STATUS = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

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

        $order = DB::transaction(function () use ($user, $validated, $items, $subtotal, $total) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'],
                'customer_address' => $validated['address'],
                'payment_method' => $validated['payment_method'],
                'subtotal' => round($subtotal, 2),
                'discount' => round($subtotal - $total, 2),
                'total' => round($total, 2),
                'status' => 'pending',
            ]);
            $order->items()->createMany($items);
            CartItem::where('user_id', $user->id)->delete();
            return $order->load('items.product');
        });

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => $this->formatOrderForUser($order),
            'cart_items' => [],
            'summary' => [
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
                'items_count' => 0,
            ],
        ], 201);
    }

    public function userOrders(Request $request)
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (Order $order) => $this->formatOrderForUser($order));

        return response()->json(['orders' => $orders]);
    }

    public function venderOrders(Request $request)
    {
        $venderId = $request->user()->id;

        $orders = Order::with([
            'items' => fn ($query) => $query->where('v_id', $venderId)->with('product'),
            'user:id,name,email',
        ])
            ->whereHas('items', fn ($query) => $query->where('v_id', $venderId))
            ->latest()
            ->get()
            ->map(function (Order $order) {
                $vendorTotal = $order->items->sum('line_total');

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'payment_method' => $order->payment_method,
                    'placed_at' => optional($order->created_at)->toDateTimeString(),
                    'customer' => [
                        'name' => $order->customer_name,
                        'email' => $order->customer_email,
                        'phone' => $order->customer_phone,
                        'address' => $order->customer_address,
                    ],
                    'products' => $order->items->map(function (OrderItem $item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->p_id,
                            'name' => $item->product_name,
                            'quantity' => (int) $item->quantity,
                            'unit_price' => (float) $item->unit_price,
                            'line_total' => (float) $item->line_total,
                        ];
                    })->values(),
                    'total_amount' => round($vendorTotal, 2),
                ];
            });

        return response()->json(['orders' => $orders]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $venderId = $request->user()->id;
        $belongsToVender = $order->items()->where('v_id', $venderId)->exists();

        if (! $belongsToVender) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        if (! in_array($validated['status'], self::VENDER_ALLOWED_STATUS, true)) {
            return response()->json(['message' => 'Invalid order status.'], 422);
        }

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
            ],
        ]);
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
                'v_id' => $product->v_id,
                'product_name' => $product->p_name,
                'unit_price' => round($finalPrice, 2),
                'quantity' => (int) $item->quantity,
                'line_total' => round($lineTotal, 2),
            ];
        })->values()->all();;

        return [$items, $subtotal, $total];
    }
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    private function formatOrderForUser(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'subtotal' => (float) $order->subtotal,
            'discount' => (float) $order->discount,
            'total' => (float) $order->total,
            'placed_at' => optional($order->created_at)->toDateTimeString(),
            'customer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'address' => $order->customer_address,
            ],
            'products' => $order->items->map(function (OrderItem $item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->p_id,
                    'name' => $item->product_name,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'line_total' => (float) $item->line_total,
                ];
            })->values(),
        ];
    }
}
