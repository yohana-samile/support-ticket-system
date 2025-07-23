<?php

namespace App\Http\Requests\Backend\Topics;

use Illuminate\Foundation\Http\FormRequest;

class TertiaryTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:150|min:5',
            'description' => 'nullable|string|max:255|min:20',
            'sub_topic_id' => 'required|exists:sub_topics,id',
            'topic_id' => 'required|exists:topics,id',
            'is_active' => 'nullable|boolean',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $tertiaryTopic = $this->route('tertiary');
            $rules['name'] = 'required|string|max:150|min:5|unique:tertiary_topics,name,'.$tertiaryTopic.',uid';
            $rules['sub_topic_id'] = 'sometimes|required|exists:sub_topics,id';
            $rules['topic_id'] = 'sometimes|required|exists:topics,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.tertiary_topic_name_required'),
            'name.max' => __('validation.name_max'),
            'name.min' => __('validation.name_min'),
            'name.unique' => __('validation.name_unique'),
            'description.max' => __('validation.description_max'),
            'description.min' => __('validation.description_min'),
            'sub_topic_id.required' => __('validation.sub_topic_id_required'),
            'sub_topic_id.exists' => __('validation.sub_topic_id_exists'),
            'topic_id.required' => __('validation.topic_id_required'),
            'topic_id.exists' => __('validation.topic_id_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('label.tertiary_topic'),
            'description' => __('label.description'),
            'sub_topic_id' => __('label.subtopic'),
            'topic_id' => __('label.topic'),
        ];
    }
}
