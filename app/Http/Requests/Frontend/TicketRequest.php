<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high,critical',
            'due_date' => 'nullable|date|after_or_equal:today',
            'attachments.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'A ticket title is required',
            'description.required' => 'A detailed description is required',
            'category_id.required' => 'Please select a category',
            'priority.required' => 'Please select a priority level',
        ];
    }
}
