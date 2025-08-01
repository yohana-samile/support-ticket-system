<?php
namespace App\Http\Controllers\Backend\Report\Ticket\Group;

use App\Exports\Tickets\Topic\ExportTopicExcel;
use App\Exports\Tickets\Topic\ExportTopicPdf;
use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\Ticket\Ticket;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class TopicGroupController extends Controller
{
    public function byTopic()
    {
        $data['statuses'] = Status::all();
        $data['title'] = "Topic Summary Count";
        $data['total_tickets'] = Ticket::count();
        return view("pages.backend.report.ticket.group.topic.by_topic", $data);
    }

    public function getTopicSummary(Request $request)
    {
        $statuses = Status::all();
        $query = Topic::query()->with('subtopics');

        $withCount = ['tickets'];
        foreach ($statuses as $status) {
            $withCount["tickets as {$status->slug}_tickets_count"] = function($q) use ($status) {
                $q->where('status', $status->slug);
            };
        }

        if ($request->start_date) {
            $query->whereHas('tickets', function($q) use ($request) {
                $q->where('created_at', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $query->whereHas('tickets', function($q) use ($request) {
                $q->where('created_at', '<=', $request->end_date);
            });
        }

        // Handle search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchTerm = strtolower($request->search['value']);

            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereHas('subtopics', function($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
                    });
            });
        }

        // Handle sorting
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            // Only allow sorting by name (column 0)
            if ($orderColumn == 0) {
                $query->orderBy('name', $orderDirection);
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        return DataTables::of($query->withCount($withCount))
            ->addColumn('name', function($topic) {
                return $topic->name;
            })
            ->addColumn('subtopics', function($topic) {
                return $topic->subtopics->pluck('name')->join(', ');
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%".strtolower($keyword)."%"]);
            })->toJson();
    }

    public function exportTicketByTopic(Request $request, Topic $topic)
    {
        $type = $request->query('type', 'excel');
        $scope = $request->query('scope', 'current');
        $topicId = $request->query('topic');

        $filters = [
            'scope' => $scope,
            'topic_id' => $scope === 'all' ? null : $topicId,
            'start_date' => $scope === 'current' ? $request->query('start_date') : null,
            'end_date' => $scope === 'current' ? $request->query('end_date') : null,
        ];
        if ($scope === 'current' && empty($topicId)) {
            abort(400, 'Topic selection is required for current view exports');
        }
        $filename = $scope === 'all'
            ? 'all-tickets-'.now()->format('Y-m-d')
            : 'tickets-topic-'.$topicId.'-'.now()->format('Y-m-d');

        switch ($type) {
            case 'excel':
                return Excel::download(
                    new ExportTopicExcel($filters),
                    $filename.'.xlsx'
                );
            case 'pdf':
                $export = new ExportTopicPdf($filters);
                if ($scope === 'all') {
                    $export->chunkSize = 500;
                }
                return $export->download($filename.'.pdf');
            default:
                abort(404, 'Invalid export type');
        }
    }

    public function ticketsByTopic(Topic $topic, Request $request)
    {
        if ($request->ajax()) {
            $query = $topic->tickets()->with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'assignedTo',]);

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

        $data['topic_selected'] = $topic->name;
        $data['topic_uid'] = $topic->uid;
        $data['title'] = "List Tickets By Topic";
        $data['total_tickets'] = $topic->tickets()->count();
        return view('pages.backend.report.ticket.group.topic.list_by_topic', $data);
    }

    public function topicData(Request $request)
    {
        $statuses = Status::all();
        $query = Topic::query();

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
            $query->whereRaw('LOWER(topics.name) LIKE ?', ["%{$searchTerm}%"]);
        }

        // Handle sorting
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            if ($orderColumn == 0) {
                $query->orderBy('topics.name', $orderDirection);
            }
        } else {
            $query->orderBy('topics.name', 'asc');
        }

        return DataTables::of($query->withCount($withCount))
            ->addColumn('name', function($mno) {
                return $mno->name;
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->whereRaw('LOWER(topics.name) LIKE ?', ["%".strtolower($keyword)."%"]);
            })->toJson();
    }
}
