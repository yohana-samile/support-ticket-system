<?php

namespace App\Models\Access\Relationship;

use App\Models\Access\Permission;
use App\Models\Access\User;
trait RoleRelationship
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')->withTimestamps();
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id')->withTimestamps();
    }
}
