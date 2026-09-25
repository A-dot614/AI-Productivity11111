@props(['label', 'value', 'icon' => null, 'hint' => null, 'trend' => null, 'tone' => 'indigo'])

@php
    $tones = [
        'indigo' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400',
        'emerald' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
        'amber' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
        'rose' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
        'sky' => 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400',
        'violet' => 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400',
    ];
@endphp

<div class="card p-5 transition-shadow hover:shadow-float">
    <div class="flex items-center justify-between">
        <p class="text-xs font-medium uppercase tracking-wide text-slate-400 dark:text-slate-500">{{ $label }}</p>
        @if ($icon)
            <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $tones[$tone] }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                </svg>
            </div>
        @endif
    </div>
    <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $value }}</p>
    @if ($hint || $trend !== null)
        <div class="mt-1.5 flex items-center gap-1.5 text-xs">
            @if ($trend !== null)
                <span @class([
                    'inline-flex items-center gap-0.5 font-semibold',
                    'text-emerald-600 dark:text-emerald-400' => $trend >= 0,
                    'text-rose-600 dark:text-rose-400' => $trend < 0,
                ])>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        @if ($trend >= 0)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8m9 0v9" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7l10 10M17 17H8M17 17V8" />
                        @endif
                    </svg>
                    {{ $trend }}
                </span>
            @endif
            @if ($hint)
                <span class="text-slate-400 dark:text-slate-500">{{ $hint }}</span>
            @endif
        </div>
    @endif
</div>
