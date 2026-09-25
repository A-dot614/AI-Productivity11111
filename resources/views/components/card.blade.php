@props(['title' => null, 'subtitle' => null, 'action' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || $action)
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
            <div>
                @if ($title)
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">{{ $subtitle }}</p>
                @endif
            </div>
            @if ($action)
                <div class="shrink-0">{!! $action !!}</div>
            @endif
        </div>
    @endif
    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>
</div>
