<?php

namespace App\Models;

use App\Models\Ticket\Ticket;

class Operator extends BaseModel
{
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_operator', 'operator_id', 'ticket_id');
    }
}
