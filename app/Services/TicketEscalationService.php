<?php

namespace App\Services;

use App\Models\Access\User;
use App\Models\Ticket;

class TicketEscalationService
{
    public function handleReopen(Ticket $ticket)
    {
        $reopenCount = $ticket->reopen_history_count + 1;
        $ticket->update(['reopen_history_count' => $reopenCount]);

        /**
         * Rule 3: Escalate if reopened > 2 times
         */
        if ($reopenCount > 2) {
            $manager = User::role('administration')->first();
            $ticket->update([
                'status' => 'escalated',
                'assigned_to' => $manager->id
            ]);
        }
    }
}
