<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateRole', $this->route('user'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::in([User::ROLE_USER, User::ROLE_ADMIN])],
        ];
    }
}
