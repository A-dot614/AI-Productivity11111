<?php

namespace App\Http\Requests\Task;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('task'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'priority' => ['sometimes', 'required', 'string', Rule::in(Task::PRIORITIES)],
            'status' => ['sometimes', 'string', Rule::in(Task::STATUSES)],
            'category_id' => ['nullable', 'uuid', Rule::exists('categories', 'id')->where(function ($query) {
                $query->where('user_id', $this->user()->id)->orWhere('is_global', true);
            })],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['uuid'],
            'due_date' => ['nullable', 'date'],
            'scheduled_at' => ['nullable', 'date'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
            'actual_minutes' => ['nullable', 'integer', 'min:0', 'max:10080'],
            'recurrence' => ['nullable', 'string', Rule::in(Task::RECURRENCES)],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $tagIds = collect($this->input('tags', []))->filter()->values();

                if ($tagIds->isNotEmpty() && $tagIds->count() !== Tag::query()->where('user_id', $this->user()->id)->whereIn('id', $tagIds)->count()) {
                    $validator->errors()->add('tags', 'One or more tags do not belong to you.');
                }
            },
        ];
    }
}
