<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:payment_channels,name',
            'code' => 'required|string|max:50|unique:payment_channels,code',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string',
            'config' => 'nullable|array',
        ];
    }
}
