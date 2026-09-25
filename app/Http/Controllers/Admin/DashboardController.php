<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminStatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected readonly AdminStatsService $stats,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->stats->overview(),
            'growth' => $this->stats->userGrowthSeries(30),
            'statuses' => $this->stats->taskStatusDistribution(),
            'aiUsage' => $this->stats->aiFeaturesUsage(30),
        ]);
    }
}
