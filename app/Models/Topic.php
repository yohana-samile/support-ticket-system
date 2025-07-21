<?php

namespace App\Models;

use App\Models\Ticket\Ticket;
use App\Models\Topic\TopicTrait;

class Topic extends BaseModel
{
    use TopicTrait;
    public function saasApp()
    {
        return $this->belongsTo(SaasApp::class, 'saas_app_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    protected $appends = ['can_be_deleted'];
}
