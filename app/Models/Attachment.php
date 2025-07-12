<?php

namespace App\Models;


use App\Models\Ticket\Ticket;

class Attachment extends BaseModel
{
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
