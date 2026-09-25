<?php

namespace App\Http\Controllers;

use App\Services\Productivity\ProductivityService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductivityController extends Controller
{
    public function __construct(
        protected readonly ProductivityService $productivity,
    ) {}

    public function index(Request $request): View
    {
        $period = $request->query('period', 'week');
        [$from, $to] = $this->resolvePeriod($period);

        $metrics = $this->productivity->metrics(auth()->user(), $from, $to);

        return view('analytics.index', [
            'period' => $period,
            'metrics' => $metrics,
            'score' => $this->productivity->score(auth()->user(), $from, $to),
            'daily' => $this->productivity->dailySeries(auth()->user(), 30),
            'weekly' => $this->productivity->weeklySeries(auth()->user(), 12),
            'categories' => $this->productivity->categoryDistribution(auth()->user(), $from, $to),
            'priorities' => $this->productivity->priorityDistribution(auth()->user(), $from, $to),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $period = $request->query('period', 'week');
        [$from, $to] = $this->resolvePeriod($period);

        return response()->json([
            'metrics' => $this->productivity->metrics(auth()->user(), $from, $to),
            'score' => $this->productivity->score(auth()->user(), $from, $to),
            'daily' => $this->productivity->dailySeries(auth()->user(), 30),
            'weekly' => $this->productivity->weeklySeries(auth()->user(), 12),
            'categories' => $this->productivity->categoryDistribution(auth()->user(), $from, $to),
            'priorities' => $this->productivity->priorityDistribution(auth()->user(), $from, $to),
        ]);
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    protected function resolvePeriod(string $period): array
    {
        return match ($period) {
            'day' => [CarbonImmutable::today()->startOfDay(), CarbonImmutable::today()->endOfDay()],
            'month' => [CarbonImmutable::today()->startOfMonth(), CarbonImmutable::today()->endOfMonth()],
            'year' => [CarbonImmutable::today()->startOfYear(), CarbonImmutable::today()->endOfYear()],
            default => [CarbonImmutable::today()->startOfWeek(), CarbonImmutable::today()->endOfWeek()],
        };
    }
}
