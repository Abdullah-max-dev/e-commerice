<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class AdminProductReportController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,resolved,rejected'],
        ]);

        $reports = ProductReport::query()
            ->with(['product:p_id,p_name,is_active', 'user:id,name', 'vendor:id,name'])
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->latest('id')
            ->paginate(30);

        return response()->json($reports);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:resolved,rejected'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
            'deactivate_product' => ['sometimes', 'boolean'],
        ]);

        $report = ProductReport::query()->with('product')->findOrFail($id);

        $report->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
            'resolved_at' => now(),
        ]);

        if ($validated['status'] === 'resolved' && ($validated['deactivate_product'] ?? false) && $report->product) {
            Product::query()->where('p_id', $report->product_id)->update(['is_active' => false]);
        }

        UserNotification::create([
            'user_id' => $report->user_id,
            'type' => 'product_report_reviewed',
            'title' => 'Your product report has been reviewed',
            'message' => "Your report for product #{$report->product_id} has been marked as {$validated['status']}.",
            'meta' => [
                'report_id' => $report->id,
                'product_id' => $report->product_id,
                'status' => $validated['status'],
            ],
        ]);

        return response()->json([
            'message' => 'Report updated successfully.',
            'report' => $report->fresh(['product:p_id,p_name,is_active', 'user:id,name', 'vendor:id,name']),
        ]);
    }
}
