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
        $rules = [
            'abbreviation' => 'required|string|max:50',
            'name' => 'required|string|max:150',
        ];

        if ($this->isMethod('POST')) {
            $rules['abbreviation'] .= '|unique:saas_apps,abbreviation';
            $rules['name'] .= '|unique:saas_apps,name';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $saasApp = $this->route('saasApp');;
            $rules['abbreviation'] .= '|unique:saas_apps,abbreviation,'.$saasApp->id;
            $rules['name'] .= '|unique:saas_apps,name,'.$saasApp->id;
        }

        return $rules;
    }


    public function messages(): array
    {
        return [
            'abbreviation.required' => 'The abbreviation field is required.',
            'abbreviation.string' => 'The abbreviation must be a string.',
            'abbreviation.max' => 'The abbreviation may not be greater than 50 characters.',
            'abbreviation.unique' => 'This abbreviation is already in use.',
            'name.required' => __('validation.name_required'),
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 150 characters.',
            'name.unique' => __('validation.name_unique'),
        ];
    }

    public function attributes(): array
    {
        return [
            'abbreviation' => 'abbreviation',
            'name' => 'name',
        ];
    }
}
