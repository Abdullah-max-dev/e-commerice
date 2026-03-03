<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderReport;
use Illuminate\Http\Request;

class OrderReportController extends Controller
{
    public function store(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) $request->user()->id) {
            return response()->json(['message' => 'Order not found.'], 404);
        }
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);
        $existingReport = OrderReport::query()
            ->where('order_id', $order->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if($existingReport){
            return response()->json(['message' => 'You have already reported this order.'], 422);
        }
        $report = OrderReport::create([
            'order_id'    =>   $order->id,
            'user_id'     =>   $request->user()->id,
            'message'     =>   $validated['message'],
            'status'      =>   'pending'
        ]);
        return response()->json([
            'message' => 'Order report submitted successfully.',
            'report' => $report,
        ], 201);

    }

    public function index(Request $request)
    {
        $reports = OrderReport::query()
            ->with(['order:id,order_number,status', 'user:id,name,email'])
            ->latest('id')
            ->paginate(30);

        return response()->json($reports);
    }
}
