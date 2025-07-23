<?php

namespace App\Models;

use App\Models\Ticket\Ticket;
use App\Models\Topic\TopicTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class Topic extends BaseModel
{
    use TopicTrait, LogsActivity;
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function saasApp()
    {
        return $this->belongsTo(SaasApp::class, 'saas_app_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    public function subtopics()
    {
        return $this->hasMany(SubTopic::class);
    }

    protected $appends = ['can_be_deleted'];

    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject')->latest();
    }
}
