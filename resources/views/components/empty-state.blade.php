@props(['icon' => null, 'title' => 'Nothing here yet', 'message' => null, 'action' => null])

<div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 px-6 py-12 text-center dark:border-slate-800">
    @if ($icon)
        <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
            </svg>
        </div>
    @endif
    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $title }}</h3>
    @if ($message)
        <p class="mt-1 max-w-sm text-sm text-slate-400 dark:text-slate-500">{{ $message }}</p>
    @endif
    @if ($action)
        <div class="mt-4">{!! $action !!}</div>
    @endif
</div>
