<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('payment_channels')->ignore($this->payment_channel->id)
            ],
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('payment_channels')->ignore($this->payment_channel->id)
            ],
            'icon' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string',
            'config' => 'nullable|array',
        ];
    }
}
