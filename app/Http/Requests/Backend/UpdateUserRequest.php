<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($this->user)
            ],
            'password' => 'sometimes|string|min:8',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'is_active' => 'nullable|boolean',
        ];
    }
}
