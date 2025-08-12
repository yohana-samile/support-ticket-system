<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreStickerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note'           => 'required|string',
            'color_code'     => 'nullable|string|max:20',
            'remind_at'      => 'nullable|date',
            'is_private'     => 'required_without:target_user_id|boolean',
            'target_user_id' => [
                'required_if:is_private,false',
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== 'all' && !\App\Models\Access\User::where('id', $value)->exists()) {
                        $fail(__('validation.the_selected_target_user_is_invalid'));
                    }
                },
            ],
        ];
    }
}
