<?php

namespace App\Models\Access;

use App\Models\Access\Attribute\PermissonGroupAttribute;
use App\Models\Access\Relationship\PermissonGroupRelation;
use Illuminate\Database\Eloquent\Model;
use App\Models\Access\Relationship\PermissionRelationship;
use App\Models\Access\Attribute\PermissionAttribute;

/**
 * Class Permission
 * @package App\Models\Access
 */
class PermissionGroup extends Model
{
    use PermissonGroupAttribute, PermissonGroupRelation;


    /**
     * @return mixed
     * Relationship
     *
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
