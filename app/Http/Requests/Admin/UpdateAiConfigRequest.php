<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAiConfigRequest extends FormRequest
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
            'ai_provider' => ['required', 'string', Rule::in(['openai', 'gemini'])],
            'ai_api_key' => ['nullable', 'string', 'max:255'],
            'ai_model' => ['nullable', 'string', 'max:120'],
            'ai_base_url' => ['nullable', 'url', 'max:255'],
            'ai_max_tokens' => ['nullable', 'integer', 'min:256', 'max:8000'],
            'ai_timeout' => ['nullable', 'integer', 'min:5', 'max:300'],
            'ai_cache_enabled' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ai_cache_enabled' => $this->boolean('ai_cache_enabled'),
        ]);
    }
}
