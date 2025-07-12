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
            'priority' => 'required|in:low,medium,high,critical',
            'attachments.*' => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf,doc,docx',

            'assigned_to' => 'required|exists:users,id',
            'client_id' => 'nullable|exists:clients,id',

            'saas_app_id' => 'required|exists:saas_apps,id',
            'topic_id' => 'nullable|exists:topics,id',
            'sub_topic_id' => 'nullable|exists:sub_topics,id',
            'tertiary_topic_id' => 'nullable|exists:tertiary_topics,id',
            'payment_channel_id' => 'nullable|exists:payment_channels,id',
            'sender_id' => 'nullable|exists:sender_ids,id',

            'notification_channels' => 'nullable|array',
            'notification_channels.*' => 'in:mail,database,sms,whatsapp',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'title.required' => 'A ticket title is required.',
            'description.required' => 'A detailed description is required.',
            'priority.required' => 'Please select a priority level.',
            'assigned_to.required' => 'Please assign a user.',
            'saas_app_id.required' => 'Please select the SaaS application.',
            'attachments.*.max' => 'Each attachment must be less than 2MB.',
            'attachments.*.mimes' => 'Allowed file types: jpg, jpeg, png, pdf, doc, docx.',
        ];
    }
}
