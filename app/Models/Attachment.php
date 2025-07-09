<?php

namespace App\Models;


class Attachment extends BaseModel
{
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
