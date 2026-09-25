<?php

namespace App\Services\Reminder;

use App\Events\ReminderTriggered;
use App\Models\Reminder;
use App\Models\Task;
use App\Models\User;
use App\Services\Setting\SettingService;

class ReminderService
{
    public function __construct(
        protected readonly SettingService $settings,
    ) {}

    /**
     * Keep the task reminder in sync with its due date.
     */
    public function syncForTask(Task $task, ?string $channel = null): void
    {
        $this->clearForTask($task);

        if ($task->due_date === null || $task->status === Task::STATUS_COMPLETED) {
            return;
        }

        $leadMinutes = max(0, (int) $this->settings->get('reminder_lead_minutes', 60));
        $remindAt = $task->due_date->copy()->subMinutes($leadMinutes);

        if ($remindAt->isPast()) {
            return;
        }

        Reminder::create([
            'user_id' => $task->user_id,
            'remindable_type' => Task::class,
            'remindable_id' => $task->id,
            'remind_at' => $remindAt,
            'channel' => $channel ?? (string) $this->settings->get('reminder_channel', Reminder::CHANNEL_APP),
            'subject' => "Task due: {$task->title}",
            'message' => "Your task \"{$task->title}\" is due at {$task->due_date->format('D, M j, Y g:i A')}.",
        ]);
    }

    public function clearForTask(Task $task): void
    {
        $task->reminders()->where('is_sent', false)->delete();
    }

    /**
     * Process all reminders whose time has come.
     */
    public function processDue(): int
    {
        $count = 0;

        Reminder::query()
            ->with(['user', 'remindable'])
            ->pending()
            ->orderBy('remind_at')
            ->chunk(100, function ($reminders) use (&$count) {
                foreach ($reminders as $reminder) {
                    ReminderTriggered::dispatch($reminder);
                    $reminder->markSent();
                    $count++;
                }
            });

        return $count;
    }

    /**
     * Schedule in-app reminders for upcoming deadlines in the given window.
     */
    public function scheduleDeadlineReminders(User $user, $from, $to): int
    {
        $count = 0;

        $tasks = $user->tasks()
            ->active()
            ->whereBetween('due_date', [$from, $to])
            ->doesntHave('reminders')
            ->get();

        foreach ($tasks as $task) {
            $this->syncForTask($task);
            $count++;
        }

        return $count;
    }
}
