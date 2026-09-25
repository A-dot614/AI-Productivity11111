<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Repositories\Contracts\NoteRepositoryInterface;
use App\Services\Notification\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected readonly NotificationService $notifications,
        protected readonly NoteRepositoryInterface $notes,
    ) {}

    public function index(): View
    {
        $notifications = Notification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $this->notifications->unreadCount(auth()->user()),
        ]);
    }

    public function markAsRead(Notification $notification): RedirectResponse
    {
        $this->authorize('view', $notification);

        $this->notifications->markAsRead($notification);

        if ($notification->link) {
            return redirect()->to($notification->link);
        }

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        $this->notifications->markAllAsRead(auth()->user());

        return back()->with('toast', ['type' => 'success', 'title' => 'All read', 'message' => 'All notifications were marked as read.']);
    }

    public function destroy(Notification $notification): RedirectResponse
    {
        $this->authorize('view', $notification);

        $notification->delete();

        return back()->with('toast', ['type' => 'success', 'title' => 'Notification removed', 'message' => 'The notification was deleted.']);
    }
}
