<x-app-layout>

    @php
        $growthChart = [
            'type' => 'line',
            'data' => [
                'labels' => array_column($growth, 'date'),
                'datasets' => [
                    ['label' => 'Users', 'data' => array_column($growth, 'users'), 'borderColor' => '#6366f1', 'backgroundColor' => '#6366f1', 'tension' => 0.35, 'fill' => false],
                    ['label' => 'Tasks', 'data' => array_column($growth, 'tasks'), 'borderColor' => '#10b981', 'backgroundColor' => '#10b981', 'tension' => 0.35, 'fill' => false],
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
                'datasets' => [['data' => array_column($aiUsage, 'value'), 'backgroundColor' => '#8b5cf6', 'borderRadius' => 4]],
            ],
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Admin dashboard</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Platform-wide health, usage and growth.</p>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total users" value="{{ $stats['total_users'] }}" tone="indigo"
            icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
        <x-stat-card label="Active now" value="{{ $stats['users_active_today'] }}" tone="emerald"
            icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        <x-stat-card label="Total tasks" value="{{ $stats['total_tasks'] }}" tone="violet"
            icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
            hint="{{ $stats['active_tasks'] }} active" />
        <x-stat-card label="AI calls" value="{{ $stats['ai_calls'] }}" tone="amber"
            icon="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
            hint="{{ $stats['ai_failures'] }} failed" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-card title="Platform growth" subtitle="New users and tasks per day (30 days)">
            <div class="h-80">
                <div x-data="chart(@json($growthChart))">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </x-card>

        <div class="grid grid-cols-1 gap-6">
            <x-card title="Task statuses">
                <div class="h-56">
                    <div x-data="chart(@json($statusesChart))">
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </x-card>

            <x-card title="AI features used" subtitle="Last 30 days">
                <div class="h-40">
                    <div x-data="chart(@json($aiUsageChart))">
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
