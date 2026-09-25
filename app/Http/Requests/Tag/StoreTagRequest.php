<?php

namespace App\Http\Requests\Tag;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Tag::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', Rule::unique('tags', 'name')->where('user_id', $this->user()->id)],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/', 'max:9'],
        ];
    }
}
