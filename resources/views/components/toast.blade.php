<div x-data x-cloak class="pointer-events-none fixed inset-x-0 top-4 z-[100] flex flex-col items-center gap-2 px-4">
    <template x-for="item in $store.toast.items" :key="item.id">
        <div x-show="item.id" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="pointer-events-auto flex w-full max-w-md items-start gap-3 rounded-2xl border bg-white p-4 shadow-float dark:bg-slate-800"
            :class="{
                'border-emerald-200 dark:border-emerald-500/30': item.type === 'success',
                'border-rose-200 dark:border-rose-500/30': item.type === 'error',
                'border-slate-200 dark:border-slate-700': item.type === 'info',
            }">
            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full"
                :class="{
                    'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-400': item.type === 'success',
                    'bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400': item.type === 'error',
                    'bg-brand-100 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400': item.type === 'info',
                }">
                <svg x-show="item.type === 'success'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                <svg x-show="item.type === 'error'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                <svg x-show="item.type === 'info'" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="item.title"></p>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400" x-text="item.message"></p>
            </div>
            <button @click="$store.toast.dismiss(item.id)" class="text-slate-400 transition-colors hover:text-slate-600 dark:hover:text-slate-200">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </template>
</div>
