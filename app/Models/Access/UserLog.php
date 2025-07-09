<?php

namespace App\Models\Access;

use App\Models\Access\Attribute\UserLogAttribute;
use App\Models\Access\Relationship\UserLogRelationship;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
