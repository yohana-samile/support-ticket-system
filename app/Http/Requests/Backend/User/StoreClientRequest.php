<?php

namespace App\Http\Requests\Backend\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'required|string|max:20',
            'saas_app_id' => 'required|exists:saas_apps,id',
            'is_active' => 'nullable|boolean',
        ];

        if ($this->isMethod('POST')) {
            $rules['name'] .= '|unique:clients,name';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $client = $this->route('client');;
            $rules['email'] .= '|unique:clients,email,' . $client->id;
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name_required'),
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already in use.',

            'phone.string' => 'The phone must be a string.',
            'phone.max' => 'The phone may not be greater than 20 characters.',

            'saas_app_id.required' => 'The SaaS app ID is required.',
            'saas_app_id.exists' => 'The selected SaaS app is invalid.',

            'is_active.boolean' => 'The active status must be a boolean.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'name',
            'email' => 'email',
            'phone' => 'phone',
            'saas_app_id' => 'SaaS app',
            'is_active' => 'active status',
        ];
    }
}
