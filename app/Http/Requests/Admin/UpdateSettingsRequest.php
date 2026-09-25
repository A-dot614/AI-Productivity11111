<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reminder_lead_minutes' => ['nullable', 'integer', 'min:1', 'max:10080'],
            'reminder_channel' => ['nullable', 'string', 'in:app,email,both'],
            'default_task_view' => ['nullable', 'string', 'in:list,board,calendar'],
            'daily_digest_enabled' => ['sometimes', 'boolean'],
            'daily_digest_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'daily_digest_enabled' => $this->boolean('daily_digest_enabled'),
        ]);
    }
}
