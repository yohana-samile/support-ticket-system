<?php

namespace App\Models;

use App\Models\Ticket\Ticket;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SaasApp extends BaseModel
{
    use LogsActivity;
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    protected $appends = ['can_be_deleted'];

    public function getCanBeDeletedAttribute() {
        return !$this->tickets()->exists();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])  // Log all attributes
            ->logOnlyDirty()  // Only log changed attributes
            ->dontSubmitEmptyLogs();
    }

    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject')->latest();
    }
}
