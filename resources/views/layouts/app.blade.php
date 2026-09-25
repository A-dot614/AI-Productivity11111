<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ (auth()->user()->theme ?? 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} — AI Productivity</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236366f1'%3E%3Cpath d='M13 10V3L4 14h7v7l9-11h-7z'/%3E%3C/svg%3E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen font-sans">
    @if (session('toast'))
        <script type="application/json" id="flash-toast">{{ json_encode(session('toast')) }}</script>
    @endif

    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
        {{-- Mobile backdrop --}}
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

        {{-- Sidebar (desktop) --}}
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 border-r border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 lg:block">
            <x-sidebar :user="auth()->user()" />
        </aside>

        {{-- Sidebar (mobile drawer) --}}
        <aside x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-40 w-64 border-r border-slate-200 bg-white shadow-float dark:border-slate-800 dark:bg-slate-900 lg:hidden">
            <x-sidebar :user="auth()->user()" />
        </aside>

        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            {{-- Topbar --}}
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/80 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/80">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="btn-ghost -ml-2 p-2 lg:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        <h1 class="truncate text-base font-bold text-slate-900 dark:text-white sm:text-lg">{{ $title ?? 'Workspace' }}</h1>
                        <p class="hidden text-xs text-slate-400 dark:text-slate-500 sm:block">{{ now()->format('l, F j, Y') }}</p>
                    </div>

                    <div class="flex items-center gap-1 sm:gap-2">
                        <x-theme-toggle />

                        {{-- Notifications bell --}}
                        <div x-data="dropdown" @click.outside="close" class="relative">
                            <button @click="toggle" class="btn-ghost relative p-2" aria-label="Notifications">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if ($unreadNotificationsCount > 0)
                                    <span class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $unreadNotificationsCount }}</span>
                                @endif
                            </button>

                            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-float dark:border-slate-700 dark:bg-slate-800">
                                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-slate-700">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Notifications</p>
                                    @if ($unreadNotificationsCount > 0)
                                        <form action="{{ route('notifications.read-all') }}" method="POST">
                                            @csrf
                                            <button class="text-xs font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">Mark all read</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @forelse ($recentNotifications as $notification)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="flex w-full gap-3 border-b border-slate-50 px-4 py-3 text-left transition-colors hover:bg-slate-50 dark:border-slate-700/50 dark:hover:bg-slate-700/40">
                                                <span class="mt-1 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-slate-300 dark:bg-slate-600' : 'bg-brand-500' }}"></span>
                                                <span class="min-w-0">
                                                    <span class="block truncate text-sm font-medium text-slate-800 dark:text-slate-100">{{ $notification->title }}</span>
                                                    <span class="block truncate text-xs text-slate-400 dark:text-slate-500">{{ $notification->body }}</span>
                                                    <span class="mt-0.5 block text-[11px] text-slate-400 dark:text-slate-600">{{ $notification->created_at->diffForHumans() }}</span>
                                                </span>
                                            </button>
                                        </form>
                                    @empty
                                        <p class="px-4 py-8 text-center text-sm text-slate-400 dark:text-slate-500">You're all caught up.</p>
                                    @endforelse
                                </div>
                                <a href="{{ route('notifications.index') }}" class="block border-t border-slate-100 px-4 py-2.5 text-center text-xs font-semibold text-brand-600 hover:bg-slate-50 dark:border-slate-700 dark:text-brand-400 dark:hover:bg-slate-700/40">
                                    View all notifications
                                </a>
                            </div>
                        </div>

                        {{-- User menu --}}
                        <div x-data="dropdown" @click.outside="close" class="relative">
                            <button @click="toggle" class="flex items-center gap-2 rounded-full p-1 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full object-cover">
                            </button>
                            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1.5 shadow-float dark:border-slate-700 dark:bg-slate-800">
                                <div class="border-b border-slate-100 px-4 py-2.5 dark:border-slate-700">
                                    <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/40">Profile settings</a>
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 dark:text-slate-300 dark:hover:bg-slate-700/40">Admin panel</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 dark:border-slate-700">
                                    @csrf
                                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-500/10">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>

            <footer class="px-4 py-4 text-center text-xs text-slate-400 dark:text-slate-600 sm:px-6">
                {{ config('app.name') }} · Final Year Project
            </footer>
        </div>
    </div>

    <x-toast />

    @stack('scripts')
</body>
</html>
