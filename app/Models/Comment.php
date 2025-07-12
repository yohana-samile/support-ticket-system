<?php

namespace App\Models;

use App\Models\Access\User;
use App\Models\Ticket\Ticket;

class Comment extends BaseModel
{
    protected $dates = ['edited_at'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
