<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('note'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:9'],
            'is_pinned' => ['sometimes', 'boolean'],
            'task_id' => ['nullable', 'uuid', Rule::exists('tasks', 'id')->where('user_id', $this->user()->id)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_pinned' => $this->boolean('is_pinned'),
        ]);
    }
}
