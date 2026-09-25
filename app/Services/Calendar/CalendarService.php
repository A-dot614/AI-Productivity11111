<?php

namespace App\Services\Calendar;

use App\Models\CalendarEvent;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;

class CalendarService
{
    /**
     * Build a flat list of calendar event objects (FullCalendar compatible)
     * for the given range, combining calendar events and task due dates.
     *
     * @return array<int, array<string, mixed>>
     */
    public function events(User $user, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $events = [];

        CalendarEvent::query()
            ->withTrashed()
            ->where('user_id', $user->id)
            ->whereBetween('start_at', [$from, $to])
            ->orderBy('start_at')
            ->get()
            ->each(function (CalendarEvent $event) use (&$events) {
                $events[] = [
                    'id' => 'event-'.$event->id,
                    'title' => $event->title,
                    'start' => $event->start_at->format('Y-m-d\TH:i:s'),
                    'end' => $event->end_at?->format('Y-m-d\TH:i:s'),
                    'allDay' => $event->is_all_day,
                    'color' => $event->color,
                    'extendedProps' => [
                        'type' => 'event',
                        'description' => $event->description,
                        'event_id' => $event->id,
                    ],
                ];
            });

        $taskColors = [
            Task::PRIORITY_LOW => '#10b981',
            Task::PRIORITY_MEDIUM => '#6366f1',
            Task::PRIORITY_HIGH => '#f59e0b',
            Task::PRIORITY_URGENT => '#ef4444',
        ];

        $user->tasks()
            ->withTrashed()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$from, $to])
            ->orderBy('due_date')
            ->get()
            ->each(function (Task $task) use (&$events, $taskColors) {
                $events[] = [
                    'id' => 'task-'.$task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->format('Y-m-d\TH:i:s'),
                    'allDay' => false,
                    'color' => $taskColors[$task->priority] ?? '#94a3b8',
                    'extendedProps' => [
                        'type' => 'task',
                        'task_id' => $task->id,
                        'status' => $task->status,
                        'priority' => $task->priority,
                        'estimated_minutes' => $task->estimated_minutes,
                    ],
                ];
            });

        return $events;
    }
}
