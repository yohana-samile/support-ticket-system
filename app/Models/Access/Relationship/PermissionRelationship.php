<?php

namespace App\Models\Access\Relationship;

use App\Models\Access\PermissionGroup;
use App\Models\Access\Role;
use App\Models\Access\User;

/**
 * Class PermissionRelationship
 * @package App\Models\Access\Relationship
 */
trait PermissionRelationship
{
    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function permissionGroup()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id');
    }

    /**
     * @return mixed
     */
    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
