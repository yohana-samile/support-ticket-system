<?php

namespace App\Models;
use App\Models\Ticket\Ticket;

class SaasApp extends BaseModel
{
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    protected $appends = ['can_be_deleted'];

    public function getCanBeDeletedAttribute() {
        return !$this->tickets()->exists();
    }
}
