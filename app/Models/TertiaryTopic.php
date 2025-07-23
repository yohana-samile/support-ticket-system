<?php

namespace App\Models;

use App\Models\Ticket\Ticket;

class TertiaryTopic extends BaseModel
{
    protected $appends = ['can_be_deleted'];

    public function getCanBeDeletedAttribute() {
        return !$this->tickets()->exists();
    }
    public function subtopic()
    {
        return $this->belongsTo(SubTopic::class, 'sub_topic_id');
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'tertiary_topic_id');
    }
    public function topic()
    {
        return $this->through('subtopic')->has('topic');
    }
}
