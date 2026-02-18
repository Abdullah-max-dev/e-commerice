<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = CartItem::with(['product.discount', 'product.mainImage'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($this->buildCartResponse($cartItems));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'p_id' => 'required|exists:products,p_id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::with('discount')->findOrFail($validated['p_id']);
        $requestedQuantity = $validated['quantity'] ?? 1;

        $cartItem = CartItem::firstOrNew([
            'user_id' => $request->user()->id,
            'p_id' => $product->p_id,
        ]);

        $newQuantity = $cartItem->exists
            ? $cartItem->quantity + $requestedQuantity
            : $requestedQuantity;

        if ($newQuantity > $product->p_stock) {
            return response()->json([
                'message' => 'Requested quantity exceeds available stock.',
            ], 422);
        }

        $cartItem->quantity = $newQuantity;
        $cartItem->save();

        $cartItems = CartItem::with(['product.discount', 'product.mainImage'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'message' => 'Product added to cart successfully.',
            ...$this->buildCartResponse($cartItems),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::with('product.discount')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($validated['quantity'] > $cartItem->product->p_stock) {
            return response()->json([
                'message' => 'Requested quantity exceeds available stock.',
            ], 422);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        $cartItems = CartItem::with(['product.discount', 'product.mainImage'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'message' => 'Cart item updated successfully.',
            ...$this->buildCartResponse($cartItems),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $cartItem = CartItem::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $cartItem->delete();

        $cartItems = CartItem::with(['product.discount', 'product.mainImage'])
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'message' => 'Cart item removed successfully.',
            ...$this->buildCartResponse($cartItems),
        ]);
    }

    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully.',
            'cart_items' => [],
            'summary' => [
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
                'items_count' => 0,
            ],
        ]);
    }

    private function buildCartResponse($cartItems)
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
                'id' => $item->id,
                'quantity' => $item->quantity,
                'product' => [
                    'p_id' => $product->p_id,
                    'p_name' => $product->p_name,
                    'p_price' => $basePrice,
                    'final_price' => $finalPrice,
                    'p_stock' => $product->p_stock,
                    'p_image' => $product->mainImage
                        ? '/uploads/products/' . $product->mainImage->image
                        : '/default-product.png',
                ],
                'line_subtotal' => round($lineSubtotal, 2),
                'line_total' => round($lineTotal, 2),
            ];
        })->values();

        return [
            'cart_items' => $items,
            'summary' => [
                'subtotal' => round($subtotal, 2),
                'discount' => round($subtotal - $total, 2),
                'total' => round($total, 2),
                'items_count' => $items->count(),
            ],
        ];
    }
}
