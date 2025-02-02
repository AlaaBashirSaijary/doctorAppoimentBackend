<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{

    public function sendNotification($userId, $title, $message)
{
    Notification::create([
        'notifiable_type' => 'App\Models\User',
        'notifiable_id' => $userId,
        'title' => $title,
        'message' => $message,
    ]);
}
    // عرض الإشعارات للمستخدم
    public function getNotifications(Request $request)
    {
        $user = $request->user(); // أو يمكنك استخدام auth()->user()
        $notifications = $user->notifications; // استرجاع الإشعارات

        return response()->json([
            'notifications' => $notifications
        ]);
    }
    public function markAsRead($notificationId)
{
    $notification = Notification::find($notificationId);

    if ($notification) {
        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'Notification marked as read']);
    }

    return response()->json(['error' => 'Notification not found'], 404);
}

}
