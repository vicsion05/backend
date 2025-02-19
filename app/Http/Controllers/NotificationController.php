<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Lấy danh sách thông báo của người dùng
    public function index()
    {
        $user = auth()->user();

        // Kiểm tra user hiện tại
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = Notification::where('user_id', $user->id)->get();

        return response()->json($notifications);
    }

    // Đánh dấu thông báo là đã đọc
    public function markAsRead($id)
    {
        \Log::info("Marking notification as read. ID: " . $id);
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Notification marked as read'], 200);
    }


    // Xóa thông báo
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();
        return response()->json(['message' => 'Notification deleted'], 200);
    }
}
