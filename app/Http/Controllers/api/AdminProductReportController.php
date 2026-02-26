<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductReport;
use App\Models\UserNotification;
use App\Models\VenderNotification;
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
        ]);

        $report = ProductReport::query()->with('product')->findOrFail($id);

        $report->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null,
            'resolved_at' => now(),
        ]);

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
    public function warnVender(Request $request, int $venderId){
        $validated = $request->validate([
            'report_id'  =>  ['nullable','integer','exist:product_reports,id'],
            'message'    =>  ['nullable','string','max:2000'],
        ]);
        $report = null;
        if(!empty($validated['report_id'])){
            $report = ProductReport::query()->with('prodyct:p_id.p_name')->findOrFail($validated['report_id']);
        }

        VenderNotification::create([
            'vender_id'   =>   $venderId,
            'type'        =>   'admin_vender_warning',
            'title'       =>    'Admin warning regarding product reports',
            'message'     =>   $validated['message'] ?? 'you have received a warning based on product report activity.',
            'meta'        =>   [
                'product_id'      =>   $report?->product_id,
                'product_name'    =>   $report?->product?->p_name,
                'report_id'       =>   $report?->id,
                'report_comment'  =>   $report?->message,
                'highlight'       =>true,
            ],
         ]);
         if($report&&(int)$report->vender_id === $venderId){
            $report->update(['vender_warning_sent'  =>  true]);
         }
         return response()->json(['message'  =>  'warning message sent to vendor indox.']);

    }
}
