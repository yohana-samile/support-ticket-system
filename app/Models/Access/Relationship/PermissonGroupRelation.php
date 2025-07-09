<?php
namespace App\Models\Access\Relationship;

use App\Models\Access\Permission;

trait PermissonGroupRelation
{
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
