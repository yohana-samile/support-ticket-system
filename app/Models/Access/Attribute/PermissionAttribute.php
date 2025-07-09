<?php

namespace App\Models\Access\Attribute;

use App\Models\Access\User;
use App\Repositories\Access\PermissionRepository;

/**
 * Class PermissionAttribute
 * @package App\Models\Access\Attribute
 */
trait PermissionAttribute
{


    /*User permission role label*/
    public function getUserPermissionRoleLabelAttribute($user_id)
    {
        $check = $this->getIsUserPermissionRoleAttribute($user_id);
        if ($check == true) {
            return "<span class='badge badge-pill badge-warning' data-toggle='tooltip' data-html='true' title='" . trans('label.administrator.users.permission_role') . "'>" . trans('label.administrator.users.permission_role') . "</span>";
        }else{
            return '';
        }
    }


    public function getIsUserPermissionRoleAttribute($user_id)
    {
        $user = User::query()->find($user_id);
        $check = (new PermissionRepository())->checkIfPermissionIsInUserRoles($user_id, $this->id);
        return $check;
    }

}
