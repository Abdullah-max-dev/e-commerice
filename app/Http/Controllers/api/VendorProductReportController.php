<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductReport;
use Illuminate\Http\Request;

class VendorProductReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = ProductReport::query()
            ->with(['product:p_id,p_name', 'user:id,name'])
            ->where('vendor_id', $request->user()->id)
            ->latest('id')
            ->paginate(20);

        return response()->json($reports);
    }

    public function justify(Request $request, int $id)
    {
        $validated = $request->validate([
            'vendor_justification' => ['required', 'string', 'max:2000'],
        ]);

        $report = ProductReport::query()
            ->where('id', $id)
            ->where('vendor_id', $request->user()->id)
            ->firstOrFail();

        $report->update([
            'vendor_justification' => $validated['vendor_justification'],
        ]);

        return response()->json([
            'message' => 'Justification submitted successfully.',
            'report' => $report,
        ]);
    }
}
