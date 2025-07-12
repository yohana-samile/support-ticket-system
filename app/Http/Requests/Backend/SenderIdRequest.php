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
        return [
            'sender_id' => 'required|string|max:11|unique:sender_ids,sender_id',
            'operator' => 'required',
            'is_active' => 'nullable|boolean',
        ];
    }
}
