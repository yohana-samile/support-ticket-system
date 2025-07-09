<?php

namespace App\Models\System;

use App\Models\System\Attribute\CodeAttribute;
use App\Models\System\Relationship\CodeRelationship;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{

    use CodeAttribute, CodeRelationship;


}
