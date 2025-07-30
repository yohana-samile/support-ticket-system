<?php

namespace App\Services;

use App\Models\Access\User;
use App\Models\Ticket\Ticket;

class TicketEscalationService
{
    const REOPEN_ESCALATION_THRESHOLD = 2;
    const STATUS_REOPENED = 'reopened';
    const STATUS_ESCALATED = 'escalated';

    public function handleReopen(Ticket $ticket)
    {
        /**
         * Only increment if this is a genuine reopen (from resolved to open/reopened)
         */
        if ($this->isGenuineReopen($ticket)) {
            $reopenCount = $ticket->reopen_history_count + 1;
            $ticket->update(['reopen_history_count' => $reopenCount]);

            /**
             * Escalate if reopened > 2 times
             */
            if ($reopenCount > self::REOPEN_ESCALATION_THRESHOLD) {
                $this->escalateTicket($ticket);
            }
        }
    }

    protected function isGenuineReopen(Ticket $ticket): bool
    {
        return $ticket->status === self::STATUS_REOPENED;
    }

    protected function escalateTicket(Ticket $ticket)
    {
        $manager = $this->findAppropriateManager($ticket);

        if ($manager) {
            $ticket->updateQuietly([
                'status' => self::STATUS_ESCALATED,
                'assigned_to' => $manager,
                'escalated_at' => now(),
                'escalation_reason' => 'Automatic escalation after multiple reopens'
            ]);
        }
    }

    protected function findAppropriateManager(Ticket $ticket)
    {
        /**
         * First try to find a manager not previously assigned
         */
        $previousManagers = $ticket->assignedTo()->pluck('assigned_to');
        $manager = User::query()->where('is_super_admin', true)->whereNotIn('id', $previousManagers)->inRandomOrder()->value('id');

        /**
         * Fallback to any manager if none found
         */
        return $manager ?? User::query()->where('is_super_admin', true)->inRandomOrder()->value('id');
    }
}
