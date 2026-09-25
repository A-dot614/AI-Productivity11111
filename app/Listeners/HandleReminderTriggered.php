<?php

namespace App\Listeners;

use App\Events\ReminderTriggered;
use App\Models\Notification;
use App\Models\Reminder;
use App\Models\Task;
use App\Notifications\ReminderNotification;
use App\Services\Notification\NotificationService;

class HandleReminderTriggered
{
    public function __construct(
        protected readonly NotificationService $notifications,
    ) {}

    public function handle(ReminderTriggered $event): void
    {
        $reminder = $event->reminder;

        $this->notifications->create(
            user: $reminder->user,
            type: Notification::TYPE_REMINDER,
            title: $reminder->subject ?? 'Task reminder',
            body: $reminder->message,
            icon: 'bell',
            link: $reminder->remindable instanceof Task
                ? route('tasks.show', $reminder->remindable)
                : null,
        );

        if (in_array($reminder->channel, [Reminder::CHANNEL_EMAIL, Reminder::CHANNEL_BOTH], true)) {
            $reminder->user->notify(new ReminderNotification($reminder));
        }
    }
}
