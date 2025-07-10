<?php
namespace App\Models;

use App\Models\Access\User;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Ticket extends BaseModel
{
    use LogsActivity;
    protected $casts = [
        'feedback_submitted_at' => 'datetime',
        'due_date' => 'datetime',
        'time_solved' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'time_reported' => 'datetime',
        'escalated_at' => 'datetime',
        'reopened_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function getStatusBadgeAttribute()
    {
        return [
            'open' => 'open',
            'in_progress' => 'in_progress',
            'resolved' => 'resolved',
            'closed' => 'closed',
        ][$this->status] ?? 'secondary';
    }

    public function getPriorityBadgeAttribute()
    {
        return [
            'low' => 'low',
            'medium' => 'medium',
            'high' => 'high',
            'critical' => 'critical',
        ][$this->priority] ?? 'secondary';
    }

    public static function generateTicketNumber(): string
    {
        do {
            // Example format: 20250708-ABCDE
            $ticketNumber = now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (self::where('ticket_number', $ticketNumber)->exists());

        return $ticketNumber;
    }

    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject')->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'status', 'priority', 'assigned_to', 'due_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('ticket');
    }
}
