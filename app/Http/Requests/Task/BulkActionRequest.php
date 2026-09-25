<?php

namespace App\Http\Requests\Task;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Task::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['complete', 'archive', 'delete', 'restore'])],
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['uuid'],
        ];
    }
}
