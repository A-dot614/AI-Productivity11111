@props(['title' => null, 'maxWidth' => 'lg', 'openEvent' => 'open-modal', 'closeEvent' => 'close-modal'])

@php
    $widths = ['sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-lg', 'xl' => 'max-w-xl', '2xl' => 'max-w-2xl'];
@endphp

<div x-data="appModal" x-show="open" x-cloak x-init="window.addEventListener('{{ $openEvent }}', () => openModal()); window.addEventListener('{{ $closeEvent }}', () => closeModal())" class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="closeModal">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full {{ $widths[$maxWidth] }} rounded-2xl border border-slate-200 bg-white shadow-float dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-700">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</h3>
                <button @click="closeModal" class="btn-ghost -mr-2 p-1.5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="max-h-[75vh] overflow-y-auto px-5 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
