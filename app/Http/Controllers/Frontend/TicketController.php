<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\TicketRequest as StoreRequest;
use App\Models\Category;
use App\Models\System\Code;
use App\Models\System\CodeValue;
use App\Notifications\TicketReopened;
use App\Repositories\Backend\CommentRepository;
use App\Repositories\Frontend\TicketRepository;
use App\Services\TicketEscalationService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketRepository;
    protected $commentRepository;

    public function __construct(TicketRepository $ticketRepository, CommentRepository $commentRepository)
    {
        $this->ticketRepository = $ticketRepository;
        $this->commentRepository = $commentRepository;
    }

    public function index()
    {
        $tickets = $this->ticketRepository->getUserTickets(user_id());
        return view('pages.frontend.ticket.index', compact('tickets'));
    }

    public function create()
    {
        $codePriorityId = Code::query()->where('name', 'Ticket Priority')->value('id');
        $data['categories'] = Category::all();
        $data['priorities'] = CodeValue::getCodeValueByCodeId($codePriorityId);

        return view('pages.frontend.ticket.create', $data);
    }

    public function store(StoreRequest $request)
    {
        $ticket = $this->ticketRepository->store($request->validated());
        return redirect()->route('frontend.ticket.show', $ticket->uid)->with('success', 'Ticket created successfully!');
    }

    public function edit($ticketUid)
    {
        $codePriorityId = Code::query()->where('name', 'Ticket Priority')->value('id');
        $data['ticket'] = $this->ticketRepository->find($ticketUid);
        $data['categories'] = Category::all();
        $data['priorities'] = CodeValue::getCodeValueByCodeId($codePriorityId);

        return view('pages.frontend.ticket.edit', $data);
    }

    public function update(StoreRequest $request, $ticketUid)
    {
        $ticket = $this->ticketRepository->update($ticketUid, $request->validated());
        return redirect()->route('frontend.ticket.show', $ticket->uid)->with('success', 'Ticket updated successfully!');
    }

    public function destroy($ticketUid)
    {
        $this->ticketRepository->delete($ticketUid);
        return redirect()->route('frontend.ticket.index')->with('success', 'Ticket deleted successfully!');
    }

    public function feedback($ticketUid)
    {
        $ticket = $this->ticketRepository->find($ticketUid);
        if (!auth()->check() || $ticket->user_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($ticket->status !== 'resolved') {
            return redirect()->route('frontend.ticket.show', $ticket->uid)->with('warning', 'You can not add feedback to this ticket!');
        }

        return view('pages.frontend.ticket.feedback', compact('ticket'));
    }

    public function show($ticketUid)
    {
        $ticket = $this->ticketRepository->find($ticketUid);
        if (!$ticket) {
            return redirect()->back()->with('error', 'No matching ticket found.');
        }
        return view('pages.frontend.ticket.show', ['ticket' => $ticket]);
    }

    public function submitFeedback(Request $request, $ticketUid)
    {
        $validated = $request->validate([
            'satisfaction' => 'required|boolean',
            'comments' => 'nullable|string|max:1000'
        ]);
        $ticket = $this->ticketRepository->find($ticketUid);
        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not found.');
        }

        /**
         * Update ticket with feedback
         */
        $ticket->withoutEvents(function () use ($ticket, $validated) {
            $ticket->update([
                'satisfaction' => $validated['satisfaction'],
                'feedback_submitted_at' => now(),
            ]);

            if (!empty($validated['comments'])) {
                $this->commentRepository->store(
                    $ticket,
                    user(),
                    ['content' => $validated['comments']]
                );
            }
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
