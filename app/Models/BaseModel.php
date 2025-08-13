<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($contact) {
            $contact->uid = str_unique();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName($this->getLogName())  // Set the log name here
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Optionally generate the log name from the model class name
    public function getLogName(): string
    {
        return strtolower(class_basename($this));
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$this->getTable()} model has been {$eventName}";
    }
}
