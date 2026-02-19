<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\VendorNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        $vendor = $request->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Vendor profile fetched successfully.',
            'data' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'email' => $vendor->email,
                'shop_logo' => $vendor->shop_logo,
                'verification_status' => $vendor->verification_status,
                'verification_submitted_at' => optional($vendor->verification_submitted_at)->toDateTimeString(),
            ],
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $vendorId = $request->user()->id;

        $itemsQuery = OrderItem::query()->where('v_id', $vendorId)->with('order:id,status,created_at');
        $totalOrders = (clone $itemsQuery)->distinct('order_id')->count('order_id');
        $pendingOrders = (clone $itemsQuery)->whereHas('order', fn (Builder $query) => $query->where('status', 'pending'))->distinct('order_id')->count('order_id');
        $totalSales = (float) (clone $itemsQuery)->whereHas('order', fn (Builder $query) => $query->whereIn('status', ['processing', 'shipped', 'delivered']))->sum('line_total');

        $activeProducts = Product::where('v_id', $vendorId)->where('p_stock', '>', 0)->count();

        $monthlyRevenue = OrderItem::query()
            ->where('v_id', $vendorId)
            ->whereHas('order', fn (Builder $query) => $query->whereYear('created_at', now()->year))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('MONTH(orders.created_at) as month_num, DATE_FORMAT(orders.created_at, "%b") as month, SUM(order_items.line_total) as total')
            ->groupBy('month_num', 'month')
            ->orderBy('month_num')
            ->get();

        $statusChart = OrderItem::query()
            ->where('v_id', $vendorId)
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->selectRaw('orders.status as status, COUNT(DISTINCT order_items.order_id) as total')
            ->groupBy('orders.status')
            ->orderByDesc('total')
            ->get();

        $recentRatings = Product::query()
            ->where('v_id', $vendorId)
            ->orderByDesc('p_id')
            ->limit(5)
            ->get(['p_id', 'p_name'])
            ->map(function (Product $product) {
                return [
                    'product_id' => $product->p_id,
                    'product_name' => $product->p_name,
                    'rating' => 5,
                    'review' => 'Great product quality and fast shipping.',
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard stats fetched successfully.',
            'data' => [
                'total_orders' => $totalOrders,
                'total_sales' => round($totalSales, 2),
                'pending_orders' => $pendingOrders,
                'active_products' => $activeProducts,
                'monthly_revenue' => $monthlyRevenue,
                'order_status' => $statusChart,
                'recent_reviews' => $recentRatings,
            ],
        ]);
    }

    public function recentOrders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $perPage = $validated['per_page'] ?? 10;
        $search = $validated['search'] ?? '';
        $vendorId = $request->user()->id;

        $orders = OrderItem::query()
            ->where('v_id', $vendorId)
            ->with('order:id,order_number,customer_name,total,status,created_at')
            ->when($search, function (Builder $query) use ($search) {
                $query->whereHas('order', function (Builder $orderQuery) use ($search) {
                    $orderQuery->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(function (OrderItem $item) {
                return [
                    'order_id' => $item->order?->order_number,
                    'customer_name' => $item->order?->customer_name,
                    'date' => optional($item->order?->created_at)->toDateString(),
                    'total' => (float) ($item->order?->total ?? 0),
                    'status' => $item->order?->status,
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Recent orders fetched successfully.',
            'data' => $orders,
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $search = $validated['search'] ?? '';
        $perPage = $validated['per_page'] ?? 8;

        $products = Product::query()
            ->where('v_id', $request->user()->id)
            ->when($search, fn (Builder $query) => $query->where('p_name', 'like', "%{$search}%"))
            ->orderByDesc('p_id')
            ->paginate($perPage)
            ->through(function (Product $product) {
                return [
                    'id' => $product->p_id,
                    'name' => $product->p_name,
                    'price' => (float) $product->p_price,
                    'stock' => (int) $product->p_stock,
                    'stock_status' => $product->p_stock > 0 ? 'in_stock' : 'out_of_stock',
                    'quick_edit_url' => "/vender/products/{$product->p_id}/edit",
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Products fetched successfully.',
            'data' => $products,
        ]);
    }

    public function notifications(Request $request): JsonResponse
    {
        $vendor = $request->user();

        $stored = VendorNotification::query()
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (VendorNotification $notification) => [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'is_read' => $notification->is_read,
                'created_at' => optional($notification->created_at)->toDateTimeString(),
            ]);

        $lowStockCount = Product::query()->where('v_id', $vendor->id)->where('p_stock', '<=', 5)->count();
        $pendingVerification = $vendor->verification_status !== 'verified';

        $systemNotifications = collect([]);

        if ($lowStockCount > 0) {
            $systemNotifications->push([
                'id' => 'low-stock',
                'type' => 'low_stock',
                'title' => 'Low stock alert',
                'message' => "{$lowStockCount} product(s) are low in stock.",
                'is_read' => false,
                'created_at' => now()->toDateTimeString(),
            ]);
        }

        if ($pendingVerification) {
            $systemNotifications->push([
                'id' => 'verification',
                'type' => 'verification',
                'title' => 'Verification pending',
                'message' => 'Your account verification is not completed yet.',
                'is_read' => false,
                'created_at' => now()->toDateTimeString(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notifications fetched successfully.',
            'data' => $systemNotifications->merge($stored)->values(),
        ]);
    }
}
