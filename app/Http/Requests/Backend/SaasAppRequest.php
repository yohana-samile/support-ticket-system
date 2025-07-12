<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class SaasAppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'abbreviation' => 'required|email|unique:saas_apps,abbreviation',
            'name' => 'required|email|unique:saas_apps,name',
            'is_active' => 'nullable|boolean',
        ];
    }
}
