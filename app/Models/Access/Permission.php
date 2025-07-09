<?php

namespace App\Models\Access;

use App\Models\BaseModel;
use App\Models\Access\Relationship\PermissionRelationship;
use App\Models\Access\Attribute\PermissionAttribute;

/**
 * Class Permission
 * @package App\Models\Access
 */
class Permission extends BaseModel
{
    use PermissionAttribute, PermissionRelationship;

    public static function getPermissionByName($permissionName)
    {
        return Permission::query()->where('name', $permissionName)->first();
    }

    public static function getAllPermissions()
    {
        return self::all();
    }

}
