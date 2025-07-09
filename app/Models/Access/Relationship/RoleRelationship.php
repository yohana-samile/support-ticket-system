<?php

namespace App\Models\Access\Relationship;

use App\Models\Access\Permission;
use App\Models\Access\User;

/**
 * Class RoleRelationship
 * @package App\Models\Access\Relationship
 */
trait RoleRelationship
{
    /**
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)
            ->orderBy('name', 'asc')->withTimestamps();

    }
}
