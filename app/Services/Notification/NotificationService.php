<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function create(
        User $user,
        string $type,
        string $title,
        ?string $body = null,
        ?string $icon = null,
        ?string $link = null,
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'icon' => $icon,
            'link' => $link,
        ]);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    public function markAllAsRead(User $user): int
    {
        return Notification::query()
            ->where('user_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public function unreadCount(User $user): int
    {
        return Notification::query()->where('user_id', $user->id)->unread()->count();
    }

    public function recent(User $user, int $limit = 10)
    {
        return Notification::query()->where('user_id', $user->id)->latest()->limit($limit)->get();
    }
}
