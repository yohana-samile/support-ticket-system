<?php

namespace App\Models;

use App\Models\Access\User;
use App\Models\Ticket\Ticket;
use App\Models\Topic\TopicTrait;

class Topic extends BaseModel
{
    use TopicTrait;
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

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    protected $appends = ['can_be_deleted'];
}
