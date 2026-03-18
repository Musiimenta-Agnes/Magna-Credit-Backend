<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /api/notifications
    public function index(Request $request)
    {
        $notifications = UserNotification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $notifications->where('is_read', false)->count(),
        ]);
    }

    // POST /api/notifications/{id}/mark-read  — marks as read, keeps in list
    public function markRead(Request $request, $id)
    {
        UserNotification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // POST /api/notifications/{id}/clear  — deletes permanently
    public function clear(Request $request, $id)
    {
        UserNotification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['success' => true]);
    }

    // POST /api/notifications/mark-all-read  — marks all as read
    public function markAllRead(Request $request)
    {
        UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // POST /api/notifications/clear-all  — deletes all
    public function clearAll(Request $request)
    {
        UserNotification::where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['success' => true]);
    }
}