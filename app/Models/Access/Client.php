<?php

namespace App\Models\Access;

use App\Models\Access\Relationship\ClientRelationship;
use App\Models\BaseModel;

class Client extends BaseModel
{
    use ClientRelationship;

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
