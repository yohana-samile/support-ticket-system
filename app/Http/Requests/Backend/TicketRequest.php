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
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high,critical',
            'attachments.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx',

            'assigned_to' => 'required|exists:users,id',

            'client_id' => 'required_without:client_name|nullable|exists:clients,id',
            'client_name' => 'required_without:client_id|string',

            'saas_app_id' => 'required|exists:saas_apps,id',
            'topic_id' => 'required|exists:topics,id',
            'sub_topic_id' => 'required|exists:sub_topics,id',
            'tertiary_topic_id' => 'nullable|exists:tertiary_topics,id',
            'payment_channel_id' => 'nullable|exists:payment_channels,id',
            'sender_id' => 'nullable|exists:sender_ids,id',
            'issue_date' => 'nullable|date|before_or_equal:today',

            'operator' => 'nullable|array',
            'operator.*' => 'exists:operators,id',

            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'in:mail,database,sms,whatsapp',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'priority.required' => 'Please select a priority level.',
            'assigned_to.required' => 'Please assign a user.',
            'saas_app_id.required' => 'Please select the SaaS application.',
            'client_id.required_without' => 'Please select a client or enter a customer name.',
            'client_name.required_without' => 'Please enter a client name or select a client.',
            'attachments.*.max' => 'Each attachment must be less than 2MB.',
            'attachments.*.mimes' => 'Allowed file types: jpg, jpeg, png, pdf, doc, docx.',
        ];
    }
}
