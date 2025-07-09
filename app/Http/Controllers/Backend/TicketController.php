<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\TicketRequest;
use App\Models\Access\User;
use App\Models\Category;
use App\Models\System\Code;
use App\Models\System\CodeValue;
use App\Repositories\Backend\TicketRepository;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
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
        $data['categories'] = Category::all();
        $data['users'] = User::query()->where('is_reporter', false)->get();
        $data['customers'] = User::where('is_reporter', true)->get();
        $data['statuses'] = CodeValue::getCodeValueByCodeId($codeStatusId);
        $data['priorities'] = CodeValue::getCodeValueByCodeId($codePriorityId);

        return view('pages.backend.ticket.create', $data);
    }


    public function store(TicketRequest $request)
    {
        $ticket = $this->ticketRepository->store($request->validated());
        return redirect()->route('backend.ticket.show', $ticket->uid)->with('success', 'Ticket created successfully!');
    }

    public function show($ticketUid)
    {
        $data['ticket'] = $this->ticketRepository->find($ticketUid);
        $data['ticket']->setRelation('activities', $data['ticket']->activities ?? collect());
        $data['users'] = User::where('is_reporter', false)->get();

        return view('pages.backend.ticket.show', $data);
    }

    public function edit($ticketUid)
    {
        $codeStatusId = Code::query()->where('name', 'Ticket Status')->value('id');
        $codePriorityId = Code::query()->where('name', 'Ticket Priority')->value('id');

        $data['ticket'] = $this->ticketRepository->find($ticketUid);

        $data['categories'] = Category::all();
        $data['users'] = User::query()->where('is_reporter', false)->get();
        $data['customers'] = User::where('is_reporter', true)->get();
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
}
