<?php

namespace App\Http\Controllers;

use App\Services\Calendar\CalendarService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        protected readonly CalendarService $calendar,
    ) {}

    public function index(): View
    {
        return view('calendar.index');
    }

    public function data(Request $request): JsonResponse
    {
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        try {
            $start = CarbonImmutable::parse($request->query('start', $start->toDateTimeString()))->startOfDay();
            $end = CarbonImmutable::parse($request->query('end', $end->toDateTimeString()))->endOfDay();
        } catch (\Throwable) {
            $start = CarbonImmutable::now()->startOfWeek();
            $end = CarbonImmutable::now()->endOfWeek();
        }

        return response()->json($this->calendar->events($request->user(), $start, $end));
    }
}
