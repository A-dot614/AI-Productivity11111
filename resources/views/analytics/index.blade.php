<x-app-layout>

    @php
        $dailyChart = [
            'type' => 'line',
            'data' => [
                'labels' => array_column($daily, 'date'),
                'datasets' => [
                    ['label' => 'Completed', 'data' => array_column($daily, 'completed'), 'borderColor' => '#6366f1', 'backgroundColor' => '#6366f1', 'tension' => 0.35, 'fill' => false],
                    ['label' => 'Planned', 'data' => array_column($daily, 'planned'), 'borderColor' => '#94a3b8', 'borderDash' => [5, 5], 'backgroundColor' => '#94a3b8', 'fill' => false],
                ],
            ],
        ];

        $weeklyChart = [
            'type' => 'bar',
            'data' => [
                'labels' => array_column($weekly, 'week'),
                'datasets' => [
                    ['label' => 'Completed', 'data' => array_column($weekly, 'completed'), 'backgroundColor' => '#10b981', 'borderRadius' => 4],
                    ['label' => 'Planned', 'data' => array_column($weekly, 'planned'), 'backgroundColor' => '#a5b4fc', 'borderRadius' => 4],
                ],
            ],
        ];

        $categoryChart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_column($categories, 'label'),
                'datasets' => [['data' => array_column($categories, 'value'), 'backgroundColor' => ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#06b6d4', '#8b5cf6']]],
            ],
        ];

        $priorityChart = [
            'type' => 'bar',
            'data' => [
                'labels' => array_column($priorities, 'label'),
                'datasets' => [['label' => 'Tasks', 'data' => array_column($priorities, 'value'), 'backgroundColor' => ['#ef4444', '#f59e0b', '#6366f1', '#10b981'], 'borderRadius' => 4]],
            ],
        ];
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Analytics</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Understand your workload and completion patterns.</p>
        </div>
        <div class="flex gap-1 rounded-xl bg-slate-100 p-1 dark:bg-slate-800">
            @foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $value => $label)
                <a href="{{ route('analytics.index', ['period' => $value]) }}"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $period === $value ? 'bg-white text-slate-800 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        <x-stat-card label="Productivity score" value="{{ $score }}/100" tone="violet"
            icon="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
        <x-stat-card label="Tasks completed" value="{{ $metrics['completed'] }}" tone="emerald"
            icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
            hint="of {{ $metrics['planned'] }} planned" />
        <x-stat-card label="Completion rate" value="{{ $metrics['completion_rate'] }}%" tone="indigo"
            icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
        <x-stat-card label="On-time rate" value="{{ $metrics['on_time_rate'] }}%" tone="sky"
            icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        <x-stat-card label="Focus time" value="{{ $metrics['focus_hours'] }}h" tone="amber"
            icon="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-card title="Daily activity" subtitle="Last 30 days">
            <div class="h-72">
                <div x-data="chart(@json($dailyChart))">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-card>

        <x-card title="Weekly performance" subtitle="Last 12 weeks">
            <div class="h-72">
                <div x-data="chart(@json($weeklyChart))">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-card>

        <x-card title="Completed by category" subtitle="This period">
            <div class="h-72">
                <div x-data="chart(@json($categoryChart))">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-card>

        <x-card title="Completed by priority" subtitle="This period">
            <div class="h-72">
                <div x-data="chart(@json($priorityChart))">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>
