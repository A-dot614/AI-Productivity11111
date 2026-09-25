<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleTaskRequest extends FormRequest
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
            'due_date' => ['required', 'date'],
        ];
    }
}
