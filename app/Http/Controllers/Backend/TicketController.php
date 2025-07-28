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
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TicketController extends Controller
{
    protected $ticketRepository;

    public function __construct()
    {
        $this->ticketRepository = app(TicketRepository::class);
    }

    public function index()
    {
        return view('pages.backend.ticket.index');
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
            "success" => true,
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
            'operators',
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

    public function getAllForDt()
    {
        return DataTables::of($this->ticketRepository->all())
            ->addColumn('ticket_number', function($ticket) {
                return '<a href="'.route('backend.ticket.show', $ticket->uid).'">'.$ticket->ticket_number.'</a>';
            })
            ->addColumn('title', function($ticket) {
                return '<a href="'.route('backend.ticket.show', $ticket->uid).'">'.Str::limit($ticket->title, 30).'</a>';
            })
            ->addColumn('topic_name', function($ticket) {
                return $ticket->topic ?
                    '<a href="'.route('backend.topic.show', $ticket->topic->uid).'">'.Str::limit($ticket->topic->name, 30).'</a>' :
                    'N/A';
            })
            ->addColumn('status_badge', function($ticket) {
                return '<span class="badge badge-'.getStatusBadgeColor($ticket->status).'">'.ucfirst($ticket->status).'</span>';
            })
            ->addColumn('priority_badge', function($ticket) {
                return '<span class="badge badge-'.getPriorityBadgeColor($ticket->priority).'">'.ucfirst($ticket->priority).'</span>';
            })
            ->addColumn('reported_by', function($ticket) {
                return $ticket->client ? $ticket->client->name : 'N/A';
            })
            ->addColumn('when_reported', function($ticket) {
                return $ticket->created_at->diffForHumans();
            })
            ->addColumn('assigned_to', function($ticket) {
                return $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned';
            })
            ->addColumn('actions', function($ticket) {
                $actions = '<a href="'.route('backend.ticket.show', $ticket->uid).'" class="text-info mr-2 text-decoration-none" title="View">
                  <i class="fas fa-eye fa-sm"></i>
               </a>';
                $actions .= '<a href="'.route('backend.ticket.edit', $ticket->uid).'" class="text-primary mr-2 text-decoration-none" title="Edit">
                      <i class="fas fa-edit fa-sm"></i>
                   </a>';

                if($ticket->can_be_deleted) {
                    $formId = 'delete-ticket-form-' . $ticket->uid;
                    $actions .= '<a href="javascript:void(0);" class="text-danger mr-2 text-decoration-none" title="Delete" onclick="confirmDelete(\''.$ticket->uid.'\')">
                    <i class="fas fa-trash fa-sm"></i>
                 </a>';

                    $actions .= '<form id="'.$formId.'" action="'.route('backend.ticket.destroy', $ticket->uid).'" method="POST" style="display: none;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '</form>';
                }
                return $actions;
            })
            ->rawColumns(['ticket_number', 'title', 'priority_badge', 'reported_by', 'when_reported', 'assigned_to', 'topic_name', 'status_badge', 'actions'])
            ->make(true);
    }
}
