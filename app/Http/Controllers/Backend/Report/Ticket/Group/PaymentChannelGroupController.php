<?php
namespace App\Http\Controllers\Backend\Report\Ticket\Group;

use App\Exports\Tickets\Channel\ExportPaymentChannelExcel;
use App\Exports\Tickets\Channel\ExportPaymentChannelPdf;
use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use App\Models\Status;
use App\Models\Ticket\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class PaymentChannelGroupController extends Controller
{
    public function byPaymentChannel()
    {
        $data['statuses'] = Status::all();
        $data['title'] = "Payment Channels Summary Count";
        $data['total_tickets'] = Ticket::count();
        return view("pages.backend.report.ticket.group.payment_channel.by_payment_channel", $data);
    }

    public function exportTicketByPaymentChannel(Request $request)
    {
        $type = $request->query('type', 'excel');
        $scope = $request->query('scope', 'current');
        $topicId = $request->query('channel');

        $filters = [
            'scope' => $scope,
            'channel' => $scope === 'all' ? null : $topicId,
            'start_date' => $scope === 'current' ? $request->query('start_date') : null,
            'end_date' => $scope === 'current' ? $request->query('end_date') : null,
        ];
        if ($scope === 'current' && empty($topicId)) {
            abort(400, 'channel selection is required for current view exports');
        }
        $filename = $scope === 'all'
            ? 'all-tickets-'.now()->format('Y-m-d')
            : 'tickets-channel-'.$topicId.'-'.now()->format('Y-m-d');

        switch ($type) {
            case 'excel':
                return Excel::download(
                    new ExportPaymentChannelExcel($filters),
                    $filename.'.xlsx'
                );
            case 'pdf':
                $export = new ExportPaymentChannelPdf($filters);
                if ($scope === 'all') {
                    $export->chunkSize = 500;
                }
                return $export->download($filename.'.pdf');
            default:
                abort(404, 'Invalid export type');
        }
    }

    public function ticketsByPaymentChannel($uid, Request $request)
    {
        $paymentChannel = PaymentChannel::where('uid', $uid)->firstOrFail();
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

        $data['paymentChannels'] = PaymentChannel::all();
        $data['title'] = "List Tickets By Payment Channel";
        $data['total_tickets'] = $paymentChannel->tickets()->count();
        return view('pages.backend.report.ticket.group.payment_channel.list_by_payment_channel', $data);
    }

    public function paymentChannelData(Request $request)
    {
        $statuses = Status::all();
        $query = PaymentChannel::query();

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
            $query->whereRaw('LOWER(payment_channels.name) LIKE ?', ["%{$searchTerm}%"]);
        }

        // Handle sorting
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            if ($orderColumn == 0) {
                $query->orderBy('payment_channels.name', $orderDirection);
            }
        } else {
            $query->orderBy('payment_channels.name', 'asc');
        }

        return DataTables::of($query->withCount($withCount))
            ->addColumn('name', function($mno) {
                return $mno->name;
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->whereRaw('LOWER(payment_channels.name) LIKE ?', ["%".strtolower($keyword)."%"]);
            })->toJson();
    }
}
