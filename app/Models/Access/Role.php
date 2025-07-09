<?php

namespace App\Models\Access;

use App\Models\BaseModel;
use App\Models\Access\Relationship\RoleRelationship;
use App\Models\Access\Attribute\RoleAttribute;

class Role extends BaseModel
{
    use  RoleAttribute, RoleRelationship, RoleAccess;
    protected $table = 'roles';

    public static function getRoleByName($roleName)
    {
        return self::query()->where('name', $roleName)->first();
    }
    public static function getRoleById($roleId)
    {
        return self::query()->where('id', $roleId)->first();
    }

    public static function getRoleByUid($roleUid)
    {
        return self::query()->where('uid', $roleUid)->first();
    }

    public static function getAllRoles(){
        return self::all();
    }
}
