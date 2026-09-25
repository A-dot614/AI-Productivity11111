<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Edit task</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $task->title }}</p>
        </div>
        <a href="{{ route('tasks.show', $task) }}" class="btn-ghost text-sm">Back to task</a>
    </div>

    <form method="POST" action="{{ route('tasks.update', $task) }}">
        @csrf
        @method('PUT')
        @include('tasks._form', ['task' => $task, 'categories' => $categories, 'tags' => $tags])

        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('tasks.show', $task) }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary">Save changes</button>
        </div>
    </form>
</x-app-layout>
