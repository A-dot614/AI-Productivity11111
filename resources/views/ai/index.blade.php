<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">AI Assistant</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Plan, prioritize and capture faster — powered by AI.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Create from request --}}
            <div class="card border-brand-200 bg-gradient-to-br from-brand-600 to-violet-600 p-6 text-white dark:border-brand-500/20">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 backdrop-blur">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 7.05l1.414-1.414M7.05 7.05L5.636 5.636M16.95 16.95l1.414 1.414" /></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold">Create a task in plain English</h2>
                        <p class="text-sm text-brand-100">The assistant parses the title, deadline and priority for you.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('ai.create-task') }}" class="mt-4">
                    @csrf
                    <textarea name="request" rows="2" required placeholder="e.g. Finish database design chapter by Friday evening, high priority"
                        class="w-full resize-none rounded-xl border-0 bg-white/95 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-white/50"></textarea>
                    <div class="mt-3 flex justify-end">
                        <button class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-brand-700 shadow transition hover:bg-brand-50">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            Create with AI
                        </button>
                    </div>
                </form>
            </div>

            {{-- Quick actions --}}
            <x-card title="Quick actions" subtitle="One-click AI workflows">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <form method="POST" action="{{ route('ai.prioritize') }}">
                        @csrf
                        <button class="w-full rounded-xl border border-slate-200 p-4 text-left transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-slate-800 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/5">
                            <div class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" /></svg>
                                Prioritize tasks
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Rank your open tasks by importance and deadline.</p>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('ai.daily-plan') }}">
                        @csrf
                        <button class="w-full rounded-xl border border-slate-200 p-4 text-left transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-slate-800 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/5">
                            <div class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Plan today
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Schedule today's tasks into time-boxed slots.</p>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('ai.weekly-plan') }}">
                        @csrf
                        <button class="w-full rounded-xl border border-slate-200 p-4 text-left transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-slate-800 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/5">
                            <div class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                Plan the week
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Distribute upcoming tasks across the next 7 days.</p>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('ai.suggestions') }}">
                        @csrf
                        <button class="w-full rounded-xl border border-slate-200 p-4 text-left transition hover:border-brand-300 hover:bg-brand-50/50 dark:border-slate-800 dark:hover:border-brand-500/40 dark:hover:bg-brand-500/5">
                            <div class="flex items-center gap-2 text-sm font-semibold text-slate-800 dark:text-slate-100">
                                <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                                Get suggestions
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Personalized productivity tips from your activity.</p>
                        </button>
                    </form>
                </div>
            </x-card>

            {{-- Session results --}}
            @if (session('ai_prioritization'))
                <x-card title="AI prioritization">
                    <ul class="space-y-3">
                        @foreach (session('ai_prioritization') as $item)
                            <li class="flex items-start gap-3 rounded-xl border border-slate-100 p-3 dark:border-slate-800">
                                <x-priority-badge :priority="$item['priority']" class="shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $item['title'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $item['reason'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif

            @if (session('ai_daily_plan'))
                @php $viewTodayLink = '<a href="'.route('tasks.index', ['due' => 'today']).'" class="text-xs font-semibold text-brand-600 dark:text-brand-400">View today</a>'; @endphp
                <x-card title="Today's AI plan" :action="$viewTodayLink">
                    <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700 dark:text-slate-300">{{ session('ai_daily_plan')['plan'] }}</p>
                    @if (session('ai_daily_plan')['note'])
                        <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">{{ session('ai_daily_plan')['note'] }}</p>
                    @endif
                </x-card>
            @endif

            @if (session('ai_weekly_plan'))
                <x-card title="Weekly AI plan">
                    <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700 dark:text-slate-300">{{ session('ai_weekly_plan') }}</p>
                </x-card>
            @endif

            @if (session('ai_suggestions'))
                <x-card title="Personalized suggestions">
                    <ul class="space-y-2">
                        @foreach (session('ai_suggestions') as $suggestion)
                            <li class="flex items-start gap-2 text-sm text-slate-600 dark:text-slate-300">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                {{ is_array($suggestion) ? ($suggestion['title'] ?? '') : $suggestion }}
                            </li>
                        @endforeach
                    </ul>
                </x-card>
            @endif

            @if (session('ai_summary'))
                <x-card title="Note summary" subtitle="Generated by AI">
                    <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700 dark:text-slate-300">{{ session('ai_summary') }}</p>
                </x-card>
            @endif
        </div>

        {{-- Side column --}}
        <div class="space-y-6">
            <x-card title="Today's tasks" subtitle="{{ count($todayTasks) }} due today">
                @if ($todayTasks->isEmpty())
                    <p class="text-sm text-slate-400">Nothing due today. Great time to get ahead.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($todayTasks as $task)
                            <li class="flex items-center gap-2 text-sm">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-brand-500"></span>
                                <a href="{{ route('tasks.show', $task) }}" class="truncate text-slate-600 hover:text-brand-600 dark:text-slate-300">{{ $task->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>

            @php $upcomingLink = '<a href="'.route('tasks.index', ['due' => 'upcoming']).'" class="text-xs font-semibold text-brand-600 dark:text-brand-400">All</a>'; @endphp
            <x-card title="Upcoming tasks" subtitle="For context" :action="$upcomingLink">
                @if ($upcomingTasks->isEmpty())
                    <p class="text-sm text-slate-400">No upcoming tasks scheduled.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($upcomingTasks as $task)
                            <li class="flex items-center gap-2 text-sm">
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full" style="background: {{ ['low' => '#10b981', 'medium' => '#6366f1', 'high' => '#f59e0b', 'urgent' => '#ef4444'][$task->priority] }}"></span>
                                <a href="{{ route('tasks.show', $task) }}" class="truncate text-slate-600 hover:text-brand-600 dark:text-slate-300">{{ $task->title }}</a>
                                <span class="ml-auto shrink-0 text-xs text-slate-400">{{ $task->due_date?->format('M j') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>

            <x-card title="Recent AI activity">
                @if ($recentHistory->isEmpty())
                    <p class="text-sm text-slate-400">Your AI requests will appear here.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($recentHistory as $history)
                            <li class="rounded-lg bg-slate-50 p-3 dark:bg-slate-800/60">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="badge-slate !px-1.5 !py-0.5 text-[10px]">{{ $history->feature }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $history->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1.5 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $history->response }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
