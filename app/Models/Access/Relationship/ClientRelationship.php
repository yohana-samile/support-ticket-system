<?php

namespace App\Models\Access\Relationship;


use App\Models\SaasApp;
use App\Models\SenderId;
use App\Models\Ticket\Ticket;
use Spatie\Activitylog\Models\Activity;

trait ClientRelationship
{
    public function senderIds()
    {
        return $this->belongsToMany(SenderId::class, 'client_sender_id')->withTimestamps();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function saasApp()
    {
        return $this->belongsTo(SaasApp::class);
    }
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }
}
