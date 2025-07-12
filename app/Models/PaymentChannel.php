<?php

namespace App\Models;

use App\Models\Ticket\Ticket;

class PaymentChannel extends BaseModel
{
    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array'
    ];

    public function ticket()
    {
        return $this->hasMany(Ticket::class);
    }
}
