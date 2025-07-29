<?php

namespace App\Models\Ticket;

use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;

trait TicketAttribute
{
    public function getStatusBadgeAttribute()
    {
        return [
            'open' => 'open',
            'in_progress' => 'in_progress',
            'resolved' => 'resolved',
            'closed' => 'closed',
            'escalated' => 'escalated'
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


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'status', 'priority', 'assigned_to'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('ticket');
    }

    public function checkIfCanBeDeleted() {
        if ($this->users || $this->permissions) {
            return false;
        }
        else{
            return true;
        }
    }

}
