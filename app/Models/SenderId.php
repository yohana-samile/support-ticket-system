<?php

namespace App\Models;

use App\Models\Access\Client;
use App\Models\Ticket\Ticket;

class SenderId extends BaseModel
{
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_sender_id')->withTimestamps();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'sender_id');
    }
}
