<?php

namespace App\Models\System;

use App\Models\System\Attribute\CodeValueAttribute;
use App\Models\System\Relationship\CodeValueRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeValue extends Model
{

    use CodeValueRelationship, CodeValueAttribute, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code_id',
        'name',
        'description',
        'reference',
        'sort',
        'isactive',
        'lang',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'reference';
    }
}
