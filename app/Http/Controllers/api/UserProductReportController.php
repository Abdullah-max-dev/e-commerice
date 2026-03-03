<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\VendorNotification;
use Illuminate\Http\Request;

class UserProductReportController extends Controller
{
   public function index(Request $request)
    {
        $reports = ProductReport::query()
            ->with(['product:p_id,p_name'])
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(20)
            ->through(fn (ProductReport $report) => [
                'id' => $report->id,
                'product_name' => $report->product?->p_name,
                'reason' => $report->reason,
                'message' => $report->message,
                'status' => $report->status,
                'is_read' => (bool) $report->reporter_read_at,
                'created_at' => optional($report->created_at)->toDateTimeString(),
            ]);

        return response()->json([
            'data' => $reports,
            'unread_count' => ProductReport::query()
                ->where('user_id', $request->user()->id)
                ->whereNull('reporter_read_at')
                ->count(),
        ]);
    }

    public function markAsRead(Request $request, int $id)
    {
        $report = ProductReport::query()
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (! $report->reporter_read_at) {
            $report->update(['reporter_read_at' => now()]);
        }
        return response()->json([
            'message'  =>  'Report marked as read.'
        ]);


    }

    public function store(Request $request, int $id)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'in:' . implode(',', ProductReport::REASONS)],
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
            'vendor_warning_sent'  =>true,
        ]);

        $product->increment('report_count');

        VendorNotification::create([
            'type' => 'product_report_warning',
            'title' => 'Warning: Product Report Received',
            'message' => "Your product {$product->p_name} was reported. Please review report details.",
            'meta' => [
                'product_id' => $product->p_id,
                'product_name' => $product->p_name,
                'report_id' => $report->id,
                'reason' => $report->reason,
                'report_comment' => $report->message,
                'status' => $report->status,
                'highlight' => true,
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
