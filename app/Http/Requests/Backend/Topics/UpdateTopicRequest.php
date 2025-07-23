<?php

namespace App\Http\Requests\Backend\Topics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150', 'min:5',
                Rule::unique('topics')->ignore($this->route('topic'))
            ],
            'saas_app_id' => ['nullable', 'integer',
                Rule::exists('saas_apps', 'id')
            ],
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:200|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The topic name is required.',
            'name.unique' => 'This topic name already exists.',
            'name.max' => 'Topic name cannot exceed 150 characters.',
            'name.min' => 'Topic name cannot be less than 5 characters.',
            'saas_app_id.exists' => 'The selected SaaS application is invalid or inactive.',
            'description.max' => 'Description cannot exceed 200 characters.',
            'description.min' => 'Description cannot less than 10 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'topic name',
            'saas_app_id' => 'SaaS application',
            'description' => 'description',
        ];
    }
}
