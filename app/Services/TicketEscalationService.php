<?php

namespace App\Services;

use App\Models\Access\User;
use App\Models\Ticket\Ticket;

class TicketEscalationService
{
    public function handleReopen(Ticket $ticket)
    {
        // Only increment if this is a genuine reopen (from resolved to open/reopened)
        if ($this->isGenuineReopen($ticket)) {
            $reopenCount = $ticket->reopen_history_count + 1;
            $ticket->update(['reopen_history_count' => $reopenCount]);

            // Rule 3: Escalate if reopened > 2 times
            if ($reopenCount > 2) {
                logger("reached 0");

                $this->escalateTicket($ticket);
            }
        }
    }

    protected function isGenuineReopen(Ticket $ticket): bool
    {
        return $ticket->status === 'reopened';
    }

    protected function escalateTicket(Ticket $ticket)
    {
        $manager = User::query()->where('is_super_admin', true)->inRandomOrder()->value('id');
        if ($manager) {
            $ticket->updateQuietly([
                'status' => 'escalated',
                'assigned_to' => $manager,
                'escalated_at' => now(),
                'escalation_reason' => 'Automatic escalation after multiple reopens'
            ]);
        }
    }
}
