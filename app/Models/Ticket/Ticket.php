<?php
namespace App\Models\Ticket;

use App\Models\BaseModel;
use Spatie\Activitylog\Traits\LogsActivity;

class Ticket extends BaseModel
{
    use LogsActivity, TicketAttribute, TicketRelationship;
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

    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'subject')->latest();
    }
}
