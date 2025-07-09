<?php

namespace App\Http\Requests\Backend;

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
            'assigned_to' =>'required|exists:users,id',
            'reported_customer' => 'nullable|string|max:200',
            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'in:mail,database,sms,whatsapp',
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
            'attachments.*.max' => 'Attachment must be less than 2MB',
            'attachments.*.mimes' => 'Allowed file types: jpg, png, pdf, doc',
        ];
    }
}
