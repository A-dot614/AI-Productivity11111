<?php

namespace App\Services\Dashboard;

use App\Models\Task;
use App\Models\User;
use App\Services\Notification\NotificationService;
use App\Services\Productivity\ProductivityService;
use Carbon\CarbonImmutable;

class DashboardService
{
    public function __construct(
        protected readonly ProductivityService $productivity,
        protected readonly NotificationService $notifications,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(User $user): array
    {
        $today = CarbonImmutable::today();
        $weekStart = $today->startOfWeek();

        $todayTasks = $user->tasks()
            ->active()
            ->whereNotNull('due_date')
            ->whereDate('due_date', $today)
            ->orderBy('due_date')
            ->with('category')
            ->get();

        $overdue = $user->tasks()->overdue()->count();

        $upcoming = $user->tasks()
            ->active()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', $today)
            ->orderBy('due_date')
            ->limit(6)
            ->with('category')
            ->get();

        $weekly = $user->tasks()
            ->whereBetween('due_date', [$weekStart, $weekStart->addWeek()])
            ->get()
            ->reject(fn (Task $task) => $task->status === Task::STATUS_ARCHIVED);
        $weeklyCompleted = $weekly->where('status', Task::STATUS_COMPLETED)->count();
        $weeklyTotal = $weekly->count();

        $score = $this->productivity->score($user, $today->subWeek(), $today);
        $metrics = $this->productivity->metrics($user, $weekStart, $today);

        $calendarPreview = $user->tasks()
            ->active()
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$today, $today->addDays(6)])
            ->orderBy('due_date')
            ->get()
            ->groupBy(fn (Task $task) => $task->due_date->format('Y-m-d'));

        return [
            'today_tasks' => $todayTasks,
            'overdue_count' => $overdue,
            'upcoming_tasks' => $upcoming,
            'weekly_completed' => $weeklyCompleted,
            'weekly_total' => $weeklyTotal,
            'weekly_progress' => $weeklyTotal > 0 ? (int) round(($weeklyCompleted / $weeklyTotal) * 100) : 0,
            'score' => $score,
            'score_trend' => $this->scoreTrend($user, $today),
            'metrics' => $metrics,
            'calendar_preview' => $calendarPreview,
            'notifications' => $this->notifications->recent($user, 6),
            'unread_count' => $this->notifications->unreadCount($user),
            'ai_recommendation' => $this->latestAiSuggestion($user),
        ];
    }

    protected function scoreTrend(User $user, CarbonImmutable $today): int
    {
        $previous = $this->productivity->score($user, $today->subWeeks(2), $today->subWeek()->subDay());
        $current = $this->productivity->score($user, $today->subWeek(), $today);

        return $current - $previous;
    }

    protected function latestAiSuggestion(User $user): ?string
    {
        return $user->aiHistories()
            ->ofFeature('productivity_suggestions')
            ->successful()
            ->latest()
            ->value('response');
    }
}
