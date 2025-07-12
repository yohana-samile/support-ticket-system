<?php

namespace App\Models\Access\Relationship;


use App\Models\SaasApp;
use App\Models\Ticket\Ticket;

trait ClientRelationship
{
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function saasApp()
    {
        return $this->belongsTo(SaasApp::class);
    }

}
