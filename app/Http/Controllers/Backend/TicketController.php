<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\TicketRequest;
use App\Models\Access\User;
use App\Models\Channel;
use App\Models\System\Code;
use App\Models\System\CodeValue;
use App\Models\Ticket\Ticket;
use App\Repositories\Backend\TicketRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TicketController extends Controller
{
    protected $ticketRepository;

    public function __construct()
    {
        $this->ticketRepository = app(TicketRepository::class);
    }

    public function index()
    {
        $tickets = $this->ticketRepository->all();
        return view('pages.backend.ticket.index', compact('tickets'));
    }

    public function create()
    {
        $codeStatusId = Code::query()->where('name', 'Ticket Status')->value('id');
        $codePriorityId = Code::query()->where('name', 'Ticket Priority')->value('id');

        $data['statuses'] = CodeValue::getCodeValueByCodeId($codeStatusId);
        $data['priorities'] = CodeValue::getCodeValueByCodeId($codePriorityId);
        $data['channels'] = Channel::query()->where('is_active', true)->get();

        return view('pages.backend.ticket.create', $data);
    }

    public function store(TicketRequest $request)
    {
        $ticket = $this->ticketRepository->store($request->validated());
        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => $ticket
        ], Response::HTTP_CREATED);
    }

    public function show($ticketUid)
    {
        $data['ticket'] = Ticket::with([
            'attachments',
            'comments.user',
            'saasApp',
            'topic',
            'statusHistory',
            'subtopic',
            'tertiaryTopic',
            'assignedTo',
            'activities' => function($query) {
                $query->with('causer')
                    ->latest()
                    ->limit(15);
            }
        ])->where('uid', $ticketUid)->firstOrFail();

        return view('pages.backend.ticket.show', $data);
    }

    public function edit($ticketUid)
    {
        $codeStatusId = Code::query()->where('name', 'Ticket Status')->value('id');
        $codePriorityId = Code::query()->where('name', 'Ticket Priority')->value('id');

        $data['ticket'] = $this->ticketRepository->find($ticketUid);

        $data['statuses'] = CodeValue::getCodeValueByCodeId($codeStatusId);
        $data['priorities'] = CodeValue::getCodeValueByCodeId($codePriorityId);

        return view('pages.backend.ticket.edit', $data);
    }

    public function update(TicketRequest $request, $ticketUid)
    {
        $ticket = $this->ticketRepository->update($ticketUid, $request->validated());
        return redirect()->route('backend.ticket.show', $ticket->uid)->with('success', 'Ticket updated successfully!');
    }

    public function destroy($ticketUid)
    {
        $this->ticketRepository->delete($ticketUid);
        return redirect()->route('backend.ticket.index')->with('success', 'Ticket deleted successfully!');
    }

    public function updateStatus(Request $request, $ticketUid)
    {
        $request->validate(['status' => 'required|in:open,in_progress,resolved,closed']);

        $this->ticketRepository->updateStatus($ticketUid, $request->status);
        return back()->with('success', 'Ticket status updated!');
    }

    public function resolveViaEmail($uid)
    {
        $status = 'resolved';
        $this->ticketRepository->updateStatus($uid, $status);
        return redirect()->route('backend.ticket.show', $uid)->with('success', 'Ticket marked as resolved.');
    }


    public function assigned()
    {
        if (auth()->user()->is_reporter) {
            abort(403);
        }

        $tickets = $this->ticketRepository->getAssignedTickets(user_id());
        return view('pages.backend.ticket.assigned', compact('tickets'));
    }

    public function reassign(Request $request, $ticketUid)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        $ticket = $this->ticketRepository->find($ticketUid);

        $this->ticketRepository->reassign(
            $ticket,
            $request->assigned_to,
            user()
        );

        return redirect()->back()
            ->with('success', 'Ticket has been reassigned successfully');
    }

    public function getClientHistory($clientId)
    {
        $history = $this->ticketRepository->getUserTickets($clientId);
        return response()->json([
            'data' => $history
        ]);
    }
}
