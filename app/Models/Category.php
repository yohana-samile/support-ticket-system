<?php

namespace App\Models;

class Category extends BaseModel
{
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
