<x-guest-layout>
    <div class="flex min-h-screen flex-col bg-slate-50 dark:bg-slate-950">
        {{-- Nav --}}
        <header class="border-b border-slate-200 bg-white/80 backdrop-blur dark:border-slate-800 dark:bg-slate-900/80">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                <a href="/" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </span>
                    <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">AI Productivity</span>
                </a>

                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary !py-2 text-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost !py-2 text-sm">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary !py-2 text-sm">Get started</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Hero --}}
        <main class="flex-1">
            <section class="mx-auto max-w-6xl px-4 pt-16 pb-12 sm:px-6 sm:pt-24">
                <div class="mx-auto max-w-3xl text-center">
                    <span class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:border-brand-500/30 dark:bg-brand-500/10 dark:text-brand-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        AI-Assisted Personal Productivity System
                    </span>
                    <h1 class="mt-5 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl dark:text-white">
                        Plan less. <span class="bg-gradient-to-r from-brand-600 to-violet-500 bg-clip-text text-transparent">Accomplish more.</span>
                    </h1>
                    <p class="mt-5 text-lg leading-relaxed text-slate-500 dark:text-slate-400">
                        A unified workspace where tasks, notes, calendar and analytics work together — with an AI assistant that plans your day, breaks down big goals and keeps you on track.
                    </p>
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary !px-6 !py-3 text-base">Open your workspace</a>
                        @else
                            <a href="{{ route('register') }}" class="btn-primary !px-6 !py-3 text-base">Start for free</a>
                            <a href="{{ route('login') }}" class="btn-secondary !px-6 !py-3 text-base">Log in</a>
                        @endauth
                    </div>
                </div>
            </section>

            {{-- Feature grid --}}
            <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6 sm:pb-24">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        ['Tasks & subtasks', 'Prioritize, schedule, break down and track tasks with recurring support and smart filters.', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['AI assistant', 'Turn plain English into tasks, get daily plans, rewrites, estimates and personalized recommendations.', 'M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 7.05l1.414-1.414M7.05 7.05L5.636 5.636M16.95 16.95l1.414 1.414'],
                        ['Markdown notes', 'Capture ideas with a markdown editor, pin favorites and ask AI to summarize or turn notes into tasks.', 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        ['Smart calendar', 'Tasks and events in one view. Drag to reschedule, click to dive into the details.', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['Reminders & digests', 'Never miss a deadline with in-app and email reminders plus a personalized morning digest.', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                        ['Analytics', 'Track completion rate, on-time rate, focus time and a daily productivity score across periods.', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ] as [$title, $desc, $icon])
                        <div class="card p-6 transition-shadow hover:shadow-float">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                            </div>
                            <h3 class="mt-4 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-slate-500 dark:text-slate-400">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-slate-200 py-6 text-center text-sm text-slate-400 dark:border-slate-800 dark:text-slate-600">
            {{ config('app.name') }} · Final Year Project · Built with Laravel
        </footer>
    </div>
</x-guest-layout>
