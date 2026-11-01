<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's in-app notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status', 'all');

        $query = $user->notifications();

        if ($status === 'unread') {
            $query->whereNull('read_at');
        } elseif ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'status', 'unreadCount'));
    }

    /**
     * Mark notification as read and redirect to its action URL.
     */
    public function go($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $targetUrl = $notification->data['action_url'] ?? route('dashboard');

        return redirect()->to($targetUrl);
    }

    /**
     * Mark a single notification as read via POST.
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count()
            ]);
        }

        return back()->with('status', 'تم تعيين الإشعار كمقروء.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0
            ]);
        }

        return back()->with('status', 'تم تعيين جميع الإشعارات كمقروءة.');
    }

    /**
     * Remove a notification.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->delete();

        return back()->with('status', 'تم حذف الإشعار بنجاح.');
    }
}
