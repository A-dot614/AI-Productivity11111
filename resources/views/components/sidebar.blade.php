@props(['user'])

@php
    $nav = [
        'workspace' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['label' => 'My Tasks', 'route' => 'tasks.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            ['label' => 'Calendar', 'route' => 'calendar.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label' => 'Notes', 'route' => 'notes.index', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
        ],
        'insights' => [
            ['label' => 'AI Assistant', 'route' => 'ai.index', 'icon' => 'M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 7.05l1.414-1.414M7.05 7.05L5.636 5.636M16.95 16.95l1.414 1.414M13.5 8.5a2.5 2.5 0 11-3 0 2.5 2.5 0 013 0zm-2 2.5v6'],
            ['label' => 'Analytics', 'route' => 'analytics.index', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
        ],
    ];

    $admin = [
        ['label' => 'Overview', 'route' => 'admin.dashboard', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
        ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['label' => 'Analytics', 'route' => 'admin.analytics', 'icon' => 'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'AI Configuration', 'route' => 'admin.ai-config.edit', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
        ['label' => 'Logs', 'route' => 'admin.logs.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
        ['label' => 'Settings', 'route' => 'admin.settings.edit', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
    ];
@endphp

<div class="flex h-full flex-col">
    <div class="flex h-16 items-center gap-2.5 px-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-600 text-white shadow-sm">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm font-bold tracking-tight text-slate-900 dark:text-white">Productivity AI</p>
            <p class="text-[11px] text-slate-400 dark:text-slate-500">Smart workspace</p>
        </div>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
        @foreach ($nav as $section => $items)
            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600">{{ $section }}</p>
                <ul class="space-y-1">
                    @foreach ($items as $item)
                        @php $active = request()->routeIs($item['route'].'*'); @endphp
                        <li>
                            <a href="{{ route($item['route']) }}" @class([
                                'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                                'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300' => $active,
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/70 dark:hover:text-slate-100' => ! $active,
                            ])>
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        @if ($user->isAdmin())
            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-600">Administration</p>
                <ul class="space-y-1">
                    @foreach ($admin as $item)
                        @php $active = request()->routeIs($item['route'].'*'); @endphp
                        <li>
                            <a href="{{ route($item['route']) }}" @class([
                                'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors',
                                'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300' => $active,
                                'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800/70 dark:hover:text-slate-100' => ! $active,
                            ])>
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </nav>

    <div class="border-t border-slate-200 p-3 dark:border-slate-800">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-2 py-2 transition-colors hover:bg-slate-100 dark:hover:bg-slate-800/70">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-9 w-9 rounded-full object-cover ring-2 ring-white dark:ring-slate-800">
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $user->name }}</p>
                <p class="truncate text-xs text-slate-400 dark:text-slate-500">{{ $user->isAdmin() ? 'Administrator' : $user->email }}</p>
            </div>
        </a>
    </div>
</div>
