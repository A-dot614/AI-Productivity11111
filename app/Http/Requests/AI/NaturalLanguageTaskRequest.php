<?php

namespace App\Http\Requests\AI;

use Illuminate\Foundation\Http\FormRequest;

class NaturalLanguageTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'request' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }
}
