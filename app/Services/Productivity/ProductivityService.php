<?php

namespace App\Services\Productivity;

use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;

class ProductivityService
{
    /**
     * Aggregate metrics for a user over a date range.
     *
     * @return array<string, mixed>
     */
    public function metrics(User $user, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $activeTasks = $user->tasks()
            ->whereNull('deleted_at')
            ->whereBetween('due_date', [$from->startOfDay(), $to->endOfDay()])
            ->get();

        $completedTasks = $user->tasks()
            ->whereNull('deleted_at')
            ->where('status', Task::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$from->startOfDay(), $to->endOfDay()])
            ->get();

        $logs = $user->productivityLogs()
            ->whereBetween('log_date', [$from->toDateString(), $to->toDateString()])
            ->get();

        $planned = $activeTasks->count();
        $completed = $completedTasks->count();

        $completionRate = $planned > 0 ? round(($completed / $planned) * 100, 1) : 0.0;

        $timedTasks = $completedTasks->filter(
            fn (Task $task) => $task->actual_minutes !== null || $task->estimated_minutes !== null
        );
        $averageCompletionTime = $timedTasks->isNotEmpty()
            ? (int) round($timedTasks->avg(fn (Task $task) => (int) ($task->actual_minutes ?? $task->estimated_minutes)))
            : null;

        $onTimeCount = $completedTasks->filter(fn (Task $task) => $task->due_date !== null && $task->completed_at?->lte($task->due_date))->count();
        $onTimeRate = $completed > 0 ? round(($onTimeCount / $completed) * 100, 1) : 0.0;

        $focusHours = round(($logs->sum('focus_minutes') / 60), 1);

        return [
            'planned' => $planned,
            'completed' => $completed,
            'completion_rate' => $completionRate,
            'on_time_rate' => $onTimeRate,
            'average_completion_time' => $averageCompletionTime,
            'focus_hours' => $focusHours,
            'estimated_minutes' => $activeTasks->sum('estimated_minutes'),
        ];
    }

    /**
     * Compute the productivity score (0-100) for a user over a period.
     */
    public function score(User $user, CarbonImmutable $from, CarbonImmutable $to): int
    {
        $metrics = $this->metrics($user, $from, $to);

        $completionComponent = $metrics['completion_rate'] * 0.5;
        $timelinessComponent = $metrics['on_time_rate'] * 0.25;

        $goalMinutes = max(1, $user->productivity_goal_minutes);
        $days = max(1, $from->diffInDays($to) + 1);
        $expectedFocus = ($goalMinutes * $days) / 60;
        $focusRatio = $expectedFocus > 0 ? ($metrics['focus_hours'] / $expectedFocus) : 0;
        $focusComponent = min(25, $focusRatio * 25);

        return (int) round(min(100, max(0, $completionComponent + $timelinessComponent + $focusComponent)));
    }

    /**
     * Daily completion series for charts.
     *
     * @return array<int, array{date: string, completed: int, planned: int}>
     */
    public function dailySeries(User $user, int $days = 30): array
    {
        $from = CarbonImmutable::today()->subDays($days - 1);
        $series = [];

        $logs = $user->productivityLogs()
            ->whereBetween('log_date', [$from->toDateString(), today()->toDateString()])
            ->get()
            ->keyBy(fn ($log) => $log->log_date->toDateString());

        foreach (CarbonPeriod::create($from, today()) as $date) {
            $log = $logs->get($date->toDateString());

            $series[] = [
                'date' => $date->format('M j'),
                'completed' => (int) ($log->completed_tasks ?? 0),
                'planned' => (int) ($log->planned_tasks ?? 0),
                'focus_minutes' => (int) ($log->focus_minutes ?? 0),
            ];
        }

        return $series;
    }

    /**
     * Weekly completion series for charts.
     *
     * @return array<int, array{week: string, completed: int, planned: int}>
     */
    public function weeklySeries(User $user, int $weeks = 12): array
    {
        $start = CarbonImmutable::today()->startOfWeek()->subWeeks($weeks - 1);
        $series = [];

        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = $start->addWeeks($i);
            $weekEnd = $weekStart->addWeek()->subSecond();

            $planned = $user->tasks()->whereBetween('due_date', [$weekStart, $weekEnd])->count();
            $completed = $user->tasks()
                ->where('status', Task::STATUS_COMPLETED)
                ->whereBetween('completed_at', [$weekStart, $weekEnd])
                ->count();

            $series[] = [
                'week' => $weekStart->format('M j'),
                'completed' => $completed,
                'planned' => $planned,
            ];
        }

        return $series;
    }

    /**
     * Completed tasks grouped by category for charts.
     *
     * @return array<int, array{label: string, value: int}>
     */
    public function categoryDistribution(User $user, CarbonImmutable $from, CarbonImmutable $to): array
    {
        return $user->tasks()
            ->where('status', Task::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$from, $to])
            ->with('category')
            ->get()
            ->groupBy(fn (Task $task) => $task->category?->name ?? 'Uncategorized')
            ->map(fn ($tasks, $label) => ['label' => $label, 'value' => $tasks->count()])
            ->sortByDesc('value')
            ->values()
            ->all();
    }

    /**
     * Priority distribution of completed tasks for charts.
     *
     * @return array<int, array{label: string, value: int}>
     */
    public function priorityDistribution(User $user, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $counts = $user->tasks()
            ->where('status', Task::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$from, $to])
            ->selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        $result = [];

        foreach (Task::PRIORITIES as $priority) {
            $result[] = ['label' => ucfirst($priority), 'value' => (int) ($counts[$priority] ?? 0)];
        }

        return $result;
    }
}
