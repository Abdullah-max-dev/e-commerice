<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\VendorNotification;
use Illuminate\Http\Request;

class UserProductReportController extends Controller
{
    public function store(Request $request, int $id)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:'.implode(',', ProductReport::REASONS)],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $product = Product::query()->where('p_id', $id)->firstOrFail();

        $hasActive = ProductReport::query()
            ->where('product_id', $product->p_id)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['pending'])
            ->exists();

        if ($hasActive) {
            return response()->json(['message' => 'You already have an active report for this product.'], 422);
        }

        $report = ProductReport::create([
            'product_id' => $product->p_id,
            'user_id' => $request->user()->id,
            'vendor_id' => $product->v_id,
            'reason' => $validated['reason'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        $product->increment('report_count');

        VendorNotification::create([
            'vendor_id' => $product->v_id,
            'type' => 'product_reported',
            'title' => 'Product report received',
            'message' => "Your product {$product->p_name} has been reported.",
            'meta' => [
                'product_id' => $product->p_id,
                'report_id' => $report->id,
            ],
        ]);

        return response()->json([
            'message' => 'Report submitted successfully.',
            'report' => $report,
        ], 201);
    }

    public function status(Request $request, int $id)
    {
        $product = Product::query()->where('p_id', $id)->firstOrFail();

        $report = ProductReport::query()
            ->where('product_id', $product->p_id)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['pending'])
            ->latest('id')
            ->first();

        return response()->json([
            'already_reported' => (bool) $report,
            'report' => $report,
        ]);
    }
}
