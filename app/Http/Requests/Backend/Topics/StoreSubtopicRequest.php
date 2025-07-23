<?php

namespace App\Http\Requests\Backend\Topics;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubtopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:150|min:5',
            'topic_id' => 'required|exists:topics,id',
            'description' => 'nullable|string|max:255|min:20',
            'is_active' => 'nullable|boolean',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $subtopic = $this->route('subtopic');
            $rules['name'] = 'required|string|max:255|unique:sub_topics,name,' . $subtopic->id;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.subtopic_name_required'),
            'name.max' => __('validation.subtopic_name_max'),
            'topic_id.required' => __('validation.please_select_topic'),
            'topic_id.exists' => __('validation.invalid_topic_selected'),
            'description.max' => __('validation.description_max'),
            'description.min' => __('validation.description_min'),
        ];
    }
}
