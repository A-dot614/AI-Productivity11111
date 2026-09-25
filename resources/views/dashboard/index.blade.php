<x-app-layout>
    @php $title = 'Dashboard'; @endphp

    @php
        $viewAllLink = '<a href="'.route('tasks.index', ['due' => 'today']).'" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">View all</a>';
        $addTaskLink = '<a href="'.route('tasks.create').'" class="btn-primary !py-2 text-xs">Add a task</a>';
        $calendarLink = '<a href="'.route('calendar.index').'" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Calendar</a>';
        $allTasksLink = '<a href="'.route('tasks.index').'" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">All tasks</a>';
        $assistantLink = '<a href="'.route('ai.index').'" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Assistant</a>';
        $notificationsLink = '<a href="'.route('notifications.index').'" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">View all</a>';
        $suggestionsForm = '<form action="'.route('ai.suggestions').'" method="POST"><input type="hidden" name="_token" value="'.csrf_token().'"><button class="btn-secondary !py-2 text-xs">Get suggestions</button></form>';
    @endphp

    {{-- Welcome banner --}}
    <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-brand-600 via-brand-500 to-violet-500 p-6 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold">Welcome back, {{ auth()->user()->name }}! 👋</h2>
            <p class="mt-1 text-sm text-brand-100">
                You have <strong class="font-semibold text-white">{{ $today_tasks->count() }}</strong> task(s) due today and <strong class="font-semibold text-white">{{ $overdue_count }}</strong> overdue.
            </p>
        </div>
        <div class="flex shrink-0 gap-2">
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2.5 text-sm font-semibold backdrop-blur transition hover:bg-white/25">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                New task
            </a>
            <a href="{{ route('ai.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-brand-700 shadow transition hover:bg-brand-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 7.05l1.414-1.414M7.05 7.05L5.636 5.636M16.95 16.95l1.414 1.414M13.5 8.5a2.5 2.5 0 11-3 0 2.5 2.5 0 013 0zm-2 2.5v6" /></svg>
                Ask AI
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Today's tasks" value="{{ $today_tasks->count() }}" tone="indigo"
            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            hint="{{ $today_tasks->where('status', 'completed')->count() }} completed" />

        <x-stat-card label="Overdue" value="{{ $overdue_count }}" tone="rose"
            icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
            hint="needs attention" />

        <x-stat-card label="Weekly progress" value="{{ $weekly_progress }}%" tone="emerald"
            icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
            hint="{{ $weekly_completed }}/{{ $weekly_total }} completed" />

        <x-stat-card label="Productivity score" value="{{ $score }}/100" tone="violet"
            icon="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
            trend="{{ $score_trend }}" hint="vs last week" />
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Today's tasks --}}
        <x-card title="Today's tasks" subtitle="{{ now()->format('l, F j') }}" class="xl:col-span-2" :action="$viewAllLink">
            @if ($today_tasks->isEmpty())
                <x-empty-state icon="M5 13l4 4L19 7" title="No tasks due today"
                    message="Enjoy the calm, or add a new task to keep momentum."
                    :action="$addTaskLink" />
            @else
                <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($today_tasks as $task)
                        <li class="flex items-center gap-3 py-3">
                            <form action="{{ route('tasks.status', $task) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" aria-label="Complete task" class="flex h-5 w-5 items-center justify-center rounded-full border-2 border-slate-300 transition hover:border-emerald-500 hover:bg-emerald-50 dark:border-slate-600 dark:hover:border-emerald-400">
                                </button>
                            </form>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('tasks.show', $task) }}" class="block truncate text-sm font-medium text-slate-800 hover:text-brand-600 dark:text-slate-100 dark:hover:text-brand-400">{{ $task->title }}</a>
                                <p class="text-xs text-slate-400">{{ $task->due_date?->format('g:i A') }} @if($task->category) · <span style="color: {{ $task->category->color }}">{{ $task->category->name }}</span> @endif</p>
                            </div>
                            <x-priority-badge :priority="$task->priority" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        {{-- Calendar preview --}}
        <x-card title="Upcoming week" subtitle="Next 7 days" class="xl:col-span-1" :action="$calendarLink">
            @if ($calendar_preview->isEmpty())
                <x-empty-state title="No upcoming deadlines" message="Your week ahead looks clear." />
            @else
                <div class="space-y-3">
                    @foreach ($calendar_preview->take(5) as $date => $tasks)
                        <div>
                            <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                {{ \Carbon\Carbon::parse($date)->format('D, M j') }}
                            </p>
                            <ul class="space-y-1">
                                @foreach ($tasks->take(2) as $task)
                                    <li class="flex items-center gap-2 rounded-lg bg-slate-50 px-3 py-1.5 text-xs dark:bg-slate-800/60">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-brand-500"></span>
                                        <a href="{{ route('tasks.show', $task) }}" class="truncate text-slate-600 hover:text-brand-600 dark:text-slate-300">{{ $task->title }}</a>
                                        <span class="ml-auto shrink-0 text-slate-400">{{ $task->due_date->format('g:i A') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Upcoming tasks --}}
        <x-card title="Upcoming tasks" :action="$allTasksLink">
            @if ($upcoming_tasks->isEmpty())
                <x-empty-state title="No upcoming tasks" message="Create tasks to see them here." />
            @else
                <ul class="space-y-2">
                    @foreach ($upcoming_tasks as $task)
                        <li class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-brand-200 hover:bg-brand-50/40 dark:border-slate-800 dark:hover:border-brand-500/30 dark:hover:bg-brand-500/5">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('tasks.show', $task) }}" class="block truncate text-sm font-medium text-slate-800 hover:text-brand-600 dark:text-slate-100">{{ $task->title }}</a>
                                <p class="text-xs text-slate-400">{{ $task->due_date->format('D, M j · g:i A') }}</p>
                            </div>
                            <x-priority-badge :priority="$task->priority" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        {{-- AI recommendation --}}
        <x-card title="AI recommendation" subtitle="Based on your recent activity"
            :action="$assistantLink">
            @if ($ai_recommendation)
                <div class="rounded-xl bg-gradient-to-br from-brand-50 to-violet-50 p-4 dark:from-brand-500/10 dark:to-violet-500/10">
                    <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700 dark:text-slate-300">{{ Str::limit($ai_recommendation, 400) }}</p>
                </div>
            @else
                <x-empty-state icon="M13 10V3L4 14h7v7l9-11h-7z" title="No AI insights yet"
                    message="Generate personalized productivity suggestions from the AI assistant."
                    :action="$suggestionsForm" />
            @endif
        </x-card>

        {{-- Recent notifications --}}
        <x-card title="Notifications" :action="$notificationsLink">
            @if ($notifications->isEmpty())
                <x-empty-state title="No notifications" message="Reminders and AI insights will appear here." />
            @else
                <ul class="space-y-2">
                    @foreach ($notifications as $notification)
                        <li class="flex items-start gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-slate-300 dark:bg-slate-600' : 'bg-brand-500' }}"></span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $notification->title }}</p>
                                <p class="text-xs text-slate-400">{{ $notification->body }}</p>
                                <p class="mt-0.5 text-[11px] text-slate-400 dark:text-slate-600">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>
</x-app-layout>
