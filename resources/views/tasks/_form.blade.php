@props(['task' => null, 'categories' => [], 'tags' => []])

@php
    $selectedTags = old('tags', $task->exists ? $task->tags->pluck('id')->all() : []);
    $isCreate = ! $task->exists;
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <x-card title="Details">
            <div class="space-y-4">
                <div>
                    <label for="title" class="label">Title <span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $task->title) }}" placeholder="e.g. Prepare project presentation"
                        class="input @error('title') input-error @enderror">
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="label">Description</label>
                    <textarea id="description" name="description" rows="6" placeholder="Add context, acceptance criteria, links..."
                        class="input @error('description') input-error @enderror">{{ old('description', $task->description) }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label">Subtasks</label>
                    <div x-data="{ subtasks: {{ json_encode(old('subtasks', $task->exists ? $task->subtasks()->where('is_completed', false)->pluck('title')->all() : [])) }} }" x-init="window.addEventListener('ai-apply-suggestion', e => { if (e.detail?.subtasks) subtasks = e.detail.subtasks })">
                        <template x-for="(subtask, index) in subtasks" :key="index">
                            <div class="mb-2 flex items-center gap-2">
                                <input type="text" x-model="subtasks[index]" :name="'subtasks[' + index + ']'" placeholder="Subtask title" class="input flex-1">
                                <button type="button" @click="subtasks.splice(index, 1)" class="btn-ghost p-2 text-rose-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" @click="subtasks.push('')" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">
                            + Add subtask
                        </button>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <div class="space-y-6">
        <x-card title="Organize">
            <div class="space-y-4">
                <div>
                    <label for="category_id" class="label">Category</label>
                    <select id="category_id" name="category_id" class="input">
                        <option value="">No category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $task->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label">Tags</label>
                    <select name="tags[]" multiple class="input h-auto py-2" size="{{ min(6, max(2, $tags->count())) }}">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" @selected(in_array($tag->id, $selectedTags))>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Hold Ctrl/Cmd to select multiple</p>
                </div>

                <div>
                    <label for="priority" class="label">Priority</label>
                    <select id="priority" name="priority" class="input">
                        @foreach (['urgent' => 'Urgent', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('priority', $task->priority ?? 'medium') == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-card>

        <x-card title="Schedule">
            <div class="space-y-4">
                <div>
                    <label for="due_date" class="label">Due date & time</label>
                    <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d\TH:i')) }}"
                        class="input @error('due_date') input-error @enderror">
                    @error('due_date') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="scheduled_at" class="label">Schedule for (timeboxing)</label>
                    <input type="datetime-local" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $task->scheduled_at?->format('Y-m-d\TH:i')) }}" class="input">
                    <p class="mt-1 text-xs text-slate-400">Blocks this task on your calendar.</p>
                </div>

                <div>
                    <label for="estimated_minutes" class="label">Estimated time (minutes)</label>
                    <input type="number" id="estimated_minutes" name="estimated_minutes" min="1" max="1440" value="{{ old('estimated_minutes', $task->estimated_minutes) }}" placeholder="e.g. 45" class="input">
                </div>

                @if (! $isCreate)
                    <div>
                        <label for="actual_minutes" class="label">Actual time (minutes)</label>
                        <input type="number" id="actual_minutes" name="actual_minutes" min="0" max="1440" value="{{ old('actual_minutes', $task->actual_minutes) }}" placeholder="Tracked when completed" class="input">
                    </div>
                @endif

                <div>
                    <label for="recurrence" class="label">Recurrence</label>
                    <select id="recurrence" name="recurrence" class="input">
                        @foreach (['none' => 'Does not repeat', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('recurrence', $task->recurrence ?? 'none') == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-card>
    </div>
</div>
