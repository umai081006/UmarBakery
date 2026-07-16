<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display listing of user notifications.
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(15);

        // Mark all as read when opening inbox
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return view('customer.notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, int $id): RedirectResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $notification->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark all notifications as read (AJAX-friendly).
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['message' => 'OK', 'unread_count' => 0]);
    }
}
