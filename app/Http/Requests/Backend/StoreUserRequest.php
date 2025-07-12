<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'specializations' => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'is_super_admin' => 'nullable|boolean',
        ];
    }
}
