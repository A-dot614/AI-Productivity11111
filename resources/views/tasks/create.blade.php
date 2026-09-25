<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">New task</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Let AI break it down for you, or add it manually.</p>
        </div>
        <a href="{{ route('tasks.index') }}" class="btn-ghost text-sm">Back to tasks</a>
    </div>

    <div x-data="taskForm()" class="mb-6">
        <div class="card border-brand-200 bg-brand-50/50 p-4 dark:border-brand-500/20 dark:bg-brand-500/5">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414M16.95 7.05l1.414-1.414M7.05 7.05L5.636 5.636M16.95 16.95l1.414 1.414" /></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Create with AI</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Describe your goal and let the assistant generate the task with subtasks and estimates.</p>
                </div>
            </div>
            <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                <input type="text" x-model="aiTitle" placeholder="e.g. Plan and prepare the final year project defense" class="input flex-1">
                <button type="button" @click="createWithAI()" :disabled="aiLoading || ! aiTitle.trim()" class="btn-primary shrink-0">
                    <span x-show="!aiLoading">Generate with AI</span>
                    <span x-show="aiLoading" x-cloak>Thinking…</span>
                </button>
            </div>
        </div>

        <div x-show="aiResult" x-cloak class="mt-4 rounded-xl border border-brand-200 bg-white p-4 dark:border-brand-500/20 dark:bg-slate-800">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">AI suggestion</p>
            <p class="mt-1 text-sm text-slate-700 dark:text-slate-300" x-text="aiResult?.title"></p>
            <ul class="mt-2 space-y-1 text-sm text-slate-600 dark:text-slate-400">
                <template x-for="s in (aiResult?.subtasks || [])" :key="s">
                    <li class="flex items-center gap-2"><span class="h-1 w-1 rounded-full bg-brand-500"></span><span x-text="s"></span></li>
                </template>
            </ul>
            <button type="button" @click="applyAIResult()" class="btn-secondary mt-3 !py-2 text-xs">Fill the form with this suggestion</button>
        </div>
    </div>

    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf
        @include('tasks._form', ['task' => $task, 'categories' => $categories, 'tags' => $tags])

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('tasks.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary">Create task</button>
        </div>
    </form>

    @push('scripts')
    <script>
        window.taskForm = function () {
            return {
                aiLoading: false,
                aiResult: null,
                createWithAI() {
                    const self = this;
                    self.aiLoading = true;
                    fetch('{{ route("ai.natural-language") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
        body: JSON.stringify({ request: this.aiTitle }),
                    })
                        .then(r => r.json())
                        .then(data => {
                            self.aiLoading = false;
                            if (data.success) {
                                self.aiResult = data.data;
                            } else {
                                window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'AI failed', message: data.message || 'Try again.' } }));
                            }
                        })
                        .catch(() => {
                            self.aiLoading = false;
                            window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', title: 'AI failed', message: 'Network error.' } }));
                        });
                },
                applyAIResult() {
                    if (!this.aiResult) return;
                    const titleInput = document.getElementById('title');
                    if (titleInput) titleInput.value = this.aiResult.title;
                    window.dispatchEvent(new CustomEvent('ai-apply-suggestion', { detail: { subtasks: this.aiResult.subtasks } }));
                    this.aiResult = null;
                    this.aiTitle = '';
                },
            };
        };
    </script>
    @endpush
</x-app-layout>
