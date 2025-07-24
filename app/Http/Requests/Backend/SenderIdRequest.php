<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class SenderIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'sender_id' => 'required|string|max:11',
            'is_active' => 'nullable|boolean',
        ];

        if ($this->isMethod('POST')) {
            $rules['sender_id'] .= '|unique:sender_ids,sender_id';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $sender = $this->route('sender');;
            $rules['sender_id'] .= '|unique:sender_ids,sender_id,'.$sender->id;
        }

        return $rules;
    }


    public function messages(): array
    {
        return [
            'sender_id.required' => 'The senderId name field is required.',
            'sender_id.string' => 'The senderId must be a string.',
            'sender_id.max' => 'The senderId may not be greater than 11 characters.',
            'sender_id.unique' => 'This senderId is already in use.',
        ];
    }

    public function attributes(): array
    {
        return [
            'sender_id' => 'senderId',
            'is_active' => 'is_active',
        ];
    }
}
