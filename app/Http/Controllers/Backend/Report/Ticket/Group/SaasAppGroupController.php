<?php
namespace App\Http\Controllers\Backend\Report\Ticket\Group;

use App\Http\Controllers\Controller;
use App\Models\SaasApp;
use App\Models\Status;
use App\Models\Ticket\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SaasAppGroupController extends Controller
{
    public function bySaasApp()
    {
        $data['statuses'] = Status::all();
        $data['title'] = "Saas App Summary Count";
        $data['total_tickets'] = Ticket::count();
        return view("pages.backend.report.ticket.group.saas_app.by_saas_app", $data);
    }

    public function ticketsBySaasApp($uid, Request $request)
    {
        $paymentChannel = SaasApp::where('uid', $uid)->firstOrFail();
        if ($request->ajax()) {
            $query = $paymentChannel->tickets()->with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'assignedTo',]);

            if ($request->start_date) {
                $query->whereDate('tickets.created_at', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $query->whereDate('tickets.created_at', '<=', $request->end_date);
            }

            $tickets = $query->get();
            return DataTables::of($tickets)
                ->addColumn('title', function($ticket) {
                    return '<a href="'.route('backend.ticket.show', $ticket->uid).'">'.Str::limit($ticket->title, 30).'</a>';
                })
                ->addColumn('status_badge', function($ticket) {
                    return '<span class="badge badge-'.getStatusBadgeColor($ticket->status).'">'.ucfirst($ticket->status).'</span>';
                })
                ->addColumn('priority_badge', function($ticket) {
                    return '<span class="badge badge-'.getPriorityBadgeColor($ticket->priority).'">'.ucfirst($ticket->priority).'</span>';
                })
                ->addColumn('when_reported', function($ticket) {
                    return $ticket->created_at->diffForHumans();
                })
                ->addColumn('assigned_to', function($ticket) {
                    return $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned';
                })
                ->rawColumns(['title', 'priority_badge', 'reported_by', 'when_reported', 'assigned_to', 'status_badge'])
                ->make(true);
        }

        $data['saas'] = SaasApp::all();
        $data['title'] = "List Tickets By Saas app";
        $data['total_tickets'] = $paymentChannel->tickets()->count();
        return view('pages.backend.report.ticket.group.saas_app.list_by_saas_app', $data);
    }

    public function saasAppData(Request $request)
    {
        $statuses = Status::all();
        $query = SaasApp::query();

        $withCount = ['tickets'];
        foreach ($statuses as $status) {
            $withCount["tickets as {$status->slug}_tickets_count"] = function($q) use ($status) {
                $q->where('tickets.status', $status->slug);
            };
        }

        if ($request->start_date) {
            $query->whereHas('tickets', function($q) use ($request) {
                $q->where('tickets.created_at', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $query->whereHas('tickets', function($q) use ($request) {
                $q->where('tickets.created_at', '<=', $request->end_date);
            });
        }

        // Handle search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchTerm = strtolower($request->search['value']);
            $query->whereRaw('LOWER(saas_apps.name) LIKE ?', ["%{$searchTerm}%"]);
        }

        // Handle sorting
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            if ($orderColumn == 0) {
                $query->orderBy('saas_apps.name', $orderDirection);
            }
        } else {
            $query->orderBy('saas_apps.name', 'asc');
        }

        return DataTables::of($query->withCount($withCount))
            ->addColumn('name', function($mno) {
                return $mno->name;
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->whereRaw('LOWER(saas_apps.name) LIKE ?', ["%".strtolower($keyword)."%"]);
            })->toJson();
    }
}
