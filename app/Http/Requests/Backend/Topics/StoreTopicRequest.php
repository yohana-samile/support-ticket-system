<?php

namespace App\Http\Requests\Backend\Topics;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:topics,name|max:150|min:5',
            'saas_app_id' => 'nullable|exists:saas_apps,id',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:200|min:10',
        ];
    }
}
