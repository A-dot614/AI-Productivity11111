<?php

namespace App\View\Composers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();

        $unreadCount = 0;
        $recent = collect();

        if ($user) {
            $recent = Notification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(6)
                ->get();

            $unreadCount = $recent->whereNull('read_at')->count();
        }

        $view->with('recentNotifications', $recent);
        $view->with('unreadNotificationsCount', $unreadCount);
    }
}
