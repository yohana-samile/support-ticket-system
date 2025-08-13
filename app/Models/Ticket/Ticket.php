<?php
namespace App\Models\Ticket;

use App\Models\BaseModel;

class Ticket extends BaseModel
{
    use TicketAttribute, TicketRelationship;
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

    protected $appends = ['can_be_deleted'];
}
