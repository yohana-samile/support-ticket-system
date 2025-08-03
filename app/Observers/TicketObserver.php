<?php

namespace App\Observers;

use App\Models\Ticket\Ticket;
use App\Notifications\SatisfactionSurvey;
use App\Notifications\TicketReopened;
use App\Services\TicketEscalationService;

class TicketObserver
{
    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket)
    {
        if ($ticket->getOriginal('status') === $ticket->status) {
            return;
        }


        $originalStatus = $ticket->getOriginal('status');
        $newStatus = $ticket->status;

        if ($originalStatus !== 'resolved' && $newStatus === 'resolved') {
            $ticket->user->notify(new SatisfactionSurvey($ticket));
            $ticket->updateQuietly(['time_solved' => now()]);
            return;
        }

        if ($originalStatus === 'resolved' && $newStatus !== 'resolved') {
            $latestComment = $ticket->comments()->latest()->first();
            if ($latestComment && $this->isNegativeReply($latestComment->body)) {
                \Illuminate\Database\Eloquent\Model::withoutEvents(function () use ($ticket) {
                    $ticket->updateQuietly([
                        'status' => 'reopened',
                        'reopened_at' => now()
                    ]);
                });

                $ticket->refresh();
                if ($ticket->assignedTo) {
                    $ticket->assignedTo->notify(new TicketReopened($ticket));
                }

                app(TicketEscalationService::class)->handleReopen($ticket);
            }
        }
    }

    private function isNegativeReply(?string $message): bool
    {
        if (empty(trim($message))) {
            return false;
        }

        $keywords = [
            'not fixed', 'still an issue', 'reopen', 'unsolved', 'not satisfied',
            'not resolved', 'still broken', 'does not work',
            'issue persists', 'problem remains', 'still a problem'
        ];
        $message = strtolower(trim($message));
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
