<?php

namespace App\Services\Admin;

use App\Models\AIHistory;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AdminStatsService
{
    /**
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        $today = CarbonImmutable::today();

        return [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', $today)->count(),
            'total_tasks' => Task::withTrashed()->count(),
            'active_tasks' => Task::active()->count(),
            'completed_tasks' => Task::completed()->count(),
            'ai_calls' => AIHistory::count(),
            'ai_failures' => AIHistory::failed()->count(),
            'users_active_today' => DB::table('sessions')
                ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
                ->distinct('user_id')
                ->count('user_id'),
        ];
    }

    /**
     * @return array<int, array{date: string, users: int, tasks: int}>
     */
    public function userGrowthSeries(int $days = 30): array
    {
        $from = CarbonImmutable::today()->subDays($days - 1);

        $usersByDay = User::query()
            ->whereDate('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $tasksByDay = Task::withTrashed()
            ->whereDate('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $series = [];

        foreach (CarbonPeriod::create($from, today()) as $date) {
            $key = $date->toDateString();
            $series[] = [
                'date' => $date->format('M j'),
                'users' => (int) ($usersByDay[$key] ?? 0),
                'tasks' => (int) ($tasksByDay[$key] ?? 0),
            ];
        }

        return $series;
    }

    /**
     * @return array<int, array{label: string, value: int}>
     */
    public function taskStatusDistribution(): array
    {
        return Task::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => ['label' => ucwords(str_replace('_', ' ', $row->status)), 'value' => (int) $row->total])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label: string, value: int}>
     */
    public function aiFeaturesUsage(int $days = 30): array
    {
        $from = CarbonImmutable::today()->subDays($days);

        return AIHistory::query()
            ->where('created_at', '>=', $from)
            ->selectRaw('feature, COUNT(*) as total')
            ->groupBy('feature')
            ->get()
            ->map(fn ($row) => ['label' => ucwords(str_replace('_', ' ', $row->feature)), 'value' => (int) $row->total])
            ->sortByDesc('value')
            ->values()
            ->all();
    }
}
