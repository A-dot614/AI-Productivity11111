<x-app-layout>

    @php
        $growthChart = [
            'type' => 'line',
            'data' => [
                'labels' => array_column($growth, 'date'),
                'datasets' => [
                    ['label' => 'Users', 'data' => array_column($growth, 'users'), 'borderColor' => '#6366f1', 'backgroundColor' => 'rgba(99,102,241,0.15)', 'tension' => 0.35, 'fill' => true],
                ],
            ],
        ];

        $statusesChart = [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_column($statuses, 'label'),
                'datasets' => [['data' => array_column($statuses, 'value'), 'backgroundColor' => ['#94a3b8', '#6366f1', '#10b981', '#f59e0b']]],
            ],
        ];

        $aiUsageChart = [
            'type' => 'bar',
            'indexAxis' => 'y',
            'data' => [
                'labels' => array_column($aiUsage, 'label'),
                'datasets' => [['label' => 'Calls', 'data' => array_column($aiUsage, 'value'), 'backgroundColor' => '#8b5cf6', 'borderRadius' => 4]],
            ],
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Analytics</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Aggregate usage across all users (last 30 days).</p>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-card title="User growth" subtitle="New users per day">
            <div class="h-80">
                <div x-data="chart(@json($growthChart))">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-card>

        <x-card title="Task status distribution">
            <div class="h-80">
                <div x-data="chart(@json($statusesChart))">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-card>

        <x-card title="AI feature usage" subtitle="Last 30 days">
            <div class="h-80">
                <div x-data="chart(@json($aiUsageChart))">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-card>

        <x-card title="Top users by completed tasks">
            @if ($topUsers->isEmpty())
                <p class="text-sm text-slate-400">No completed tasks yet.</p>
            @else
                <ul class="space-y-3">
                    @foreach ($topUsers as $user)
                        <li class="flex items-center gap-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <span class="min-w-0 flex-1 truncate text-sm font-medium text-slate-700 dark:text-slate-200">{{ $user->name }}</span>
                            <span class="badge-green">{{ $user->completed_count }} done</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>
</x-app-layout>
