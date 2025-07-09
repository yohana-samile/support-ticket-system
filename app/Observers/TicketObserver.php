<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Notifications\SatisfactionSurvey;
use App\Notifications\TicketReopened;
use App\Services\TicketEscalationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TicketObserver
{
    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket)
    {
        $originalStatus = $ticket->getOriginal('status');
        $newStatus = $ticket->status;

        if ($originalStatus !== 'resolved' && $newStatus === 'resolved') {
            $ticket->user->notify(new SatisfactionSurvey($ticket));
            return;
        }

        if ($originalStatus === 'resolved' && $newStatus !== 'resolved') {
            $latestComment = $ticket->comments()->latest()->first();
            if ($latestComment && $this->isNegativeReply($latestComment->body)) {
                $ticket->withoutEvents(function () use ($ticket) {
                    $ticket->update(['status' => 'reopened']);
                });

                if ($ticket->assignedTo) {
                    $ticket->assignedTo->notify(new TicketReopened($ticket));
                }

                app(TicketEscalationService::class)->handleReopen($ticket);
                return;
            }
        }
    }

    /**
     * Check for negative keywords in replies
     */
    private function isNegativeReply(string $message): bool
    {
        $keywords = ['not fixed', 'still an issue', 'reopen', 'unsolved'];
        return Str::contains(strtolower($message), $keywords);
    }
}
