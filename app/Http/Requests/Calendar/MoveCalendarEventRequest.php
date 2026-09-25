<?php

namespace App\Http\Requests\Calendar;

use Illuminate\Foundation\Http\FormRequest;

class MoveCalendarEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('event'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date'],
        ];
    }
}
