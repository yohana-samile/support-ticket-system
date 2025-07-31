<?php

namespace App\Models;

use App\Models\Ticket\Ticket;

class PaymentChannel extends BaseModel
{
    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
