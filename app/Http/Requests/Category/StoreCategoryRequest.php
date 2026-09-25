<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Category::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/', 'max:9'],
            'icon' => ['nullable', 'string', 'max:32'],
        ];
    }
}
