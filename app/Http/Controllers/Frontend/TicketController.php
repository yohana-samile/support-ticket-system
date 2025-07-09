<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Access\User;
use App\Models\Ticket;
use App\Notifications\TicketReopened;
use App\Repositories\Backend\TicketRepository;
use App\Services\TicketEscalationService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function feedback(Ticket $ticket)
    {
        if (auth()->check() && $ticket->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $data['ticket'] = $this->ticketRepository->find($ticket);

        return view('pages.frontend.ticket.feedback', $data);
    }

    public function show($ticketUid)
    {
        $ticket = $this->ticketRepository->find($ticketUid);
        if (!$ticket) {
            return redirect()->back()->with('error', 'No matching ticket found.');
        }
        $ticket->setRelation('activities', $ticket->activities ?? collect());
        return view('pages.frontend.ticket.feedback', ['ticket' => $ticket]);
    }

    public function submitFeedback(Request $request, $ticketUid)
    {
        $validated = $request->validate([
            'satisfaction' => 'required|boolean',
            'comments' => 'nullable|string|max:1000'
        ]);
        $ticket = $this->ticketRepository->find($ticketUid);

        /**
         * Update ticket with feedback
         */
        $ticket->withoutEvents(function () use ($ticket, $validated) {
            $ticket->update([
                'satisfaction' => $validated['satisfaction'],
                'feedback_comments' => $validated['comments'] ?? null,
                'feedback_submitted_at' => now()
            ]);
        });

        /**
         * If not satisfied, reopen the ticket
         */
        if (!$validated['satisfaction']) {
            $ticket->withoutEvents(function () use ($ticket) {
                $ticket->update(['status' => 'reopened']);
            });

            if ($ticket->assignedTo) {
                $ticket->assignedTo->notify(new TicketReopened($ticket));
            }

            app(TicketEscalationService::class)->handleReopen($ticket);
        }
        return redirect()->back()->with('success', 'Feedback submitted!');
    }
}
