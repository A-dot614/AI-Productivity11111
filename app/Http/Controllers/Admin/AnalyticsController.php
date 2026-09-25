<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admin\AdminStatsService;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(
        protected readonly AdminStatsService $stats,
    ) {}

    public function index(): View
    {
        $from = CarbonImmutable::today()->subDays(30);

        return view('admin.analytics', [
            'growth' => $this->stats->userGrowthSeries(30),
            'statuses' => $this->stats->taskStatusDistribution(),
            'aiUsage' => $this->stats->aiFeaturesUsage(30),
            'topUsers' => User::query()
                ->withCount(['tasks as completed_count' => fn ($q) => $q->where('status', 'completed')])
                ->orderByDesc('completed_count')
                ->limit(8)
                ->get(),
        ]);
    }
}
