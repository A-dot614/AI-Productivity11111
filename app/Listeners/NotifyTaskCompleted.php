<?php

namespace App\Listeners;

use App\Events\TaskCompleted;
use App\Models\Notification;
use App\Services\Notification\NotificationService;

class NotifyTaskCompleted
{
    public function __construct(
        protected readonly NotificationService $notifications,
    ) {}

    public function handle(TaskCompleted $event): void
    {
        $task = $event->task;

        $this->notifications->create(
            user: $task->user,
            type: Notification::TYPE_TASK,
            title: 'Task completed',
            body: "You completed \"{$task->title}\".",
            icon: 'check-circle',
            link: route('tasks.show', $task),
        );
    }
}
