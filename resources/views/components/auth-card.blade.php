@props(['title' => null, 'subtitle' => null])

<div class="flex min-h-screen flex-col items-center justify-center bg-slate-50 px-4 py-10 dark:bg-slate-950">
    <div class="w-full max-w-md">
        <a href="/" class="mb-6 flex items-center justify-center gap-2.5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-500 text-white shadow">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </span>
            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">AI Productivity</span>
        </a>

        @if ($title || $subtitle)
            <div class="mb-5 text-center">
                @if ($title)
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $title }}</h1>
                @endif
                @if ($subtitle)
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
                @endif
            </div>
        @endif

        <div class="card p-6 sm:p-8">
            {{ $slot }}
        </div>

        @if (Route::has('register') || Route::has('login'))
            <p class="mt-5 text-center text-sm text-slate-500 dark:text-slate-400">
                @if (Route::currentRouteName() === 'login' && Route::has('register'))
                    Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">Sign up</a>
                @elseif (Route::currentRouteName() === 'register' && Route::has('login'))
                    Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline dark:text-brand-400">Log in</a>
                @endif
            </p>
        @endif
    </div>
</div>
