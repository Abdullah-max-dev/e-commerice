<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VendorNotification;
use Illuminate\Http\Request;

class VendorMessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = VendorNotification::query()
            ->where('vendor_id', $request->user()->id)
            ->where('is_archived', false)
            ->latest('id')
            ->paginate(20)
            ->through(fn (VendorNotification $message) => [
                'id' => $message->id,
                'title' => $message->title,
                'type' => $message->type,
                'message' => $message->message,
                'product_name' => $message->meta['product_name'] ?? null,
                'reason' => $message->meta['reason'] ?? null,
                'report_comment' => $message->meta['report_comment'] ?? null,
                'status' => $message->meta['status'] ?? null,
                'is_read' => $message->is_read,
                'is_warning' => (bool) ($message->meta['highlight'] ?? false),
                'created_at' => optional($message->created_at)->toDateTimeString(),
            ]);

        return response()->json([
            'data' => $messages,
            'unread_count' => VendorNotification::query()->where('vendor_id', $request->user()->id)->where('is_archived', false)->where('is_read', false)->count(),
        ]);
    }

    public function markAsRead(Request $request, int $id)
    {
        $message = VendorNotification::query()->where('vendor_id', $request->user()->id)->findOrFail($id);
        $message->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['message' => 'Message marked as read.']);
    }

    public function archive(Request $request, int $id)
    {
        $message = VendorNotification::query()->where('vendor_id', $request->user()->id)->findOrFail($id);
        $message->update(['is_archived' => true, 'archived_at' => now()]);

        return response()->json(['message' => 'Message archived.']);
    }

    public function destroy(Request $request, int $id)
    {
        $message = VendorNotification::query()->where('vendor_id', $request->user()->id)->findOrFail($id);
        $message->delete();

        return response()->json(['message' => 'Message deleted.']);
    }
}
