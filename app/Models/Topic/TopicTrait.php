<?php

namespace App\Models\Topic;

use Spatie\Activitylog\LogOptions;

trait TopicTrait
{
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
}
