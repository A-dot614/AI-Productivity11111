<?php

namespace App\Observers;

use App\Events\TaskCompleted;
use App\Models\Task;
use App\Repositories\Contracts\ProductivityLogRepositoryInterface;
use App\Services\Reminder\ReminderService;
use Carbon\CarbonImmutable;

class TaskObserver
{
    public function __construct(
        protected readonly ReminderService $reminders,
        protected readonly ProductivityLogRepositoryInterface $logs,
    ) {}

    public function created(Task $task): void
    {
        $this->reminders->syncForTask($task);
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('due_date')) {
            $this->reminders->syncForTask($task);
        }

        $this->handleStatusTransition($task);
    }

    public function deleted(Task $task): void
    {
        $this->reminders->clearForTask($task);
    }

    public function restored(Task $task): void
    {
        $this->reminders->syncForTask($task);
    }

    protected function handleStatusTransition(Task $task): void
    {
        if ($task->wasChanged('status') && $task->status === Task::STATUS_COMPLETED) {
            $this->onCompleted($task);

            return;
        }

        if ($task->wasChanged('status') && $task->getOriginal('status') === Task::STATUS_COMPLETED) {
            $this->onReopened($task);
        }
    }

    protected function onCompleted(Task $task): void
    {
        $this->logs->incrementCompleted(
            userId: $task->user_id,
            date: $task->completed_at?->toDateString() ?? today(),
            taskId: 0,
            focusMinutes: $task->actual_minutes ?? 0,
        );

        $this->reminders->clearForTask($task);

        TaskCompleted::dispatch($task);

        $this->maybeCreateRecurrence($task);
    }

    protected function onReopened(Task $task): void
    {
        $originalCompletedAt = $task->getOriginal('completed_at');

        $this->logs->decrementCompleted(
            userId: $task->user_id,
            date: $originalCompletedAt ? CarbonImmutable::parse($originalCompletedAt)->toDateString() : today(),
            taskId: 0,
        );
    }

    protected function maybeCreateRecurrence(Task $task): void
    {
        if ($task->recurrence === Task::RECURRENCE_NONE || $task->due_date === null) {
            return;
        }

        $nextDue = match ($task->recurrence) {
            Task::RECURRENCE_DAILY => $task->due_date->copy()->addDay(),
            Task::RECURRENCE_WEEKLY => $task->due_date->copy()->addWeek(),
            Task::RECURRENCE_MONTHLY => $task->due_date->copy()->addMonth(),
            default => null,
        };

        if ($nextDue === null) {
            return;
        }

        $task->children()->create([
            'user_id' => $task->user_id,
            'category_id' => $task->category_id,
            'title' => $task->title,
            'description' => $task->description,
            'priority' => $task->priority,
            'due_date' => $nextDue,
            'estimated_minutes' => $task->estimated_minutes,
            'recurrence' => $task->recurrence,
            'status' => Task::STATUS_PENDING,
        ]);
    }
}
