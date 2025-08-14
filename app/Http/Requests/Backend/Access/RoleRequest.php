<?php

namespace App\Http\Requests\Backend\Access;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends  FormRequest
{
    public  function authorize()
    {
        return true;
    }


    public function rules()
    {
        $input = $this->all();
        $basic = [];
        $optional = [];
        $array = [];
        $action_type = $input['action_type'];

        switch ($action_type) {
            case 1:
                // When Adding
                $basic = [
                    'description' => 'required|max:250',
                    'display_name' => [
                        'required',
                        Rule::unique('roles')->where(function ($query) {
                            return $query->whereNull('deleted_at');
                        }),
                    ],
                    'permissions' => 'array',
                    'permissions.*' => 'exists:permissions,id',
                    'isactive' => 'nullable',
                    'isadmin' => 'nullable',
                ];
                break;
            case 2:
                // When Editing
                $resource_id = $input['resource_id'];
                $basic = [
                    'description' => 'required|max:250',
                    'display_name' => [
                        'required',
                        'string',
                        'max:30',
                        Rule::unique('roles')
                            ->ignore($resource_id)
                            ->where(function ($query) {
                                return $query->whereNull('deleted_at');
                            }),
                    ],
                    'isactive' => 'nullable',
                    'isadmin' => 'nullable',
                    'permissions' => 'array',
                    'permissions.*' => 'exists:permissions,id',
                ];
                break;
        }
        return array_merge($basic, $optional);
    }
}
