<?php

namespace App\Models;

use App\Models\Access\User;
use App\Models\Ticket\Ticket;

class TicketStatusHistory extends BaseModel
{
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    protected $dates = [
        'changed_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
