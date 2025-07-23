<?php

namespace App\Models;


class SubTopic extends BaseModel
{
    protected $appends = ['can_be_deleted'];

    public function getCanBeDeletedAttribute() {
        return !$this->tertiaryTopics()->exists();
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
    public function tertiaryTopics()
    {
        return $this->hasMany(TertiaryTopic::class);
    }
}
