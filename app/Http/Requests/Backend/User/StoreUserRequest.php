<?php

namespace App\Http\Requests\Backend\User;

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
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'exists:topics,id',
            'role_id' => 'required|array',
            'role_id.*' => 'exists:roles,id',
            'is_active' => 'nullable|boolean',
            'is_super_admin' => 'nullable|boolean',
        ];
    }
}
