<?php

namespace App\Http\Requests\Calendar;

use App\Models\CalendarEvent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCalendarEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', CalendarEvent::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'is_all_day' => ['sometimes', 'boolean'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/', 'max:9'],
            'task_id' => ['nullable', 'uuid', Rule::exists('tasks', 'id')->where('user_id', $this->user()->id)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_all_day' => $this->boolean('is_all_day'),
        ]);
    }
}
