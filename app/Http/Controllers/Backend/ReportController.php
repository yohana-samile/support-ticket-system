<?php

namespace App\Http\Controllers\Backend;

use App\Exports\Tickets\AllReportExport;
use App\Exports\Tickets\MnoSummaryExport;
use App\Exports\Tickets\PaymentChannelSummaryExport;
use App\Exports\Tickets\SaasAppSummaryExport;
use App\Exports\Tickets\TopicSummaryExport;
use App\Http\Controllers\Controller;
use App\Models\Access\User;
use App\Models\Operator;
use App\Models\PaymentChannel;
use App\Models\SaasApp;
use App\Models\Status;
use App\Models\System\Code;
use App\Models\System\CodeValue;
use App\Models\Ticket\Ticket;
use App\Models\Topic;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    public function index()
    {
        return view("pages.backend.report.ticket.index", [
            'summaryCounts' => [
                'saas_apps' => SaasApp::count(),
                'topics' => Topic::count(),
                'mnos' => Operator::count(),
                'payment_channels' => PaymentChannel::count(),
                'total_tickets' => Ticket::count()
            ]
        ]);
    }

    public function allReport()
    {
        return view("pages.backend.report.ticket.all_report", ['title' => 'All tickets reports', 'total_tickets' => Ticket::count()]);
    }

    public function reportBy()
    {
        return view("pages.backend.report.ticket.index" , [
            'summaryCounts' => [
                'saas_apps' => SaasApp::count(),
                'topics' => Topic::count(),
                'mnos' => Operator::count(),
                'payment_channels' => PaymentChannel::count(),
                'total_tickets' => Ticket::count()
            ]
        ]);
    }

    public function summary(Request $request)
    {
        $type = $request->query('type');
        $statuses = Status::all();

        switch ($type) {
            case 'saas_app':
                return view('pages.backend.report.partials.saas_app_summary', [
                    'title' => 'Saas Applications Summary',
                    'statuses' => $statuses
                ]);
            case 'topic':
                return view('pages.backend.report.partials.topic_summary', [
                    'title' => 'Topic Summary',
                    'statuses' => $statuses
                ]);

            case 'mno':
                return view('pages.backend.report.partials.mno_summary', [
                    'title' => 'MNOs Summary',
                    'statuses' => $statuses
                ]);

            case 'payment_channel':
                return view('pages.backend.report.partials.payment_channel_summary', [
                    'title' => 'Payment Channels Summary',
                    'statuses' => $statuses
                ]);
            default:
                abort(404);
        }
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

    public function byFilter(Request $request)
    {
        $codeId = Code::query()->where('name', 'Ticket Priority')->value('id');
        $data['staff'] = User::query()->where('is_active', true)->orderBy('name')->get();
        $data['priorities'] = CodeValue::query()->where('code_id', $codeId)->get();
        $data['statues'] = Status::orderBy('name')->get();

        $data['mnos'] = Operator::orderBy('name')->get();
        $data['paymentChannels'] = PaymentChannel::orderBy('name')->get();

        return view('pages.backend.report.ticket.filter_report', $data);
    }

    public function saasAppData(Request $request)
    {
        $statuses = Status::all();
        $query = SaasApp::query();

        $withCount = ['tickets'];
        foreach ($statuses as $status) {
            $withCount["tickets as {$status->slug}_tickets_count"] = function($q) use ($status) {
                $q->where('status', $status->slug);
            };
        }

        // Date filtering
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
                    ->orWhereRaw('LOWER(abbreviation) LIKE ?', ["%{$searchTerm}%"]);
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
            ->addColumn('name', function($saasApp) {
                return $saasApp->name;
            })
            ->addColumn('abbreviation', function($saasApp) {
                return $saasApp->abbreviation;
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%".strtolower($keyword)."%"]);
            })->toJson();
    }

    public function getPaymentChannelSummary(Request $request)
    {
        $statuses = Status::all();
        $query = PaymentChannel::query();

        $withCount = ['tickets'];
        foreach ($statuses as $status) {
            $withCount["tickets as {$status->slug}_tickets_count"] = function($q) use ($status) {
                $q->where('status', $status->slug);
            };
        }

        // Date filtering
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
            $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"]);
        }

        // Handle sorting
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            if ($orderColumn == 0) {
                $query->orderBy('name', $orderDirection);
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        return DataTables::of($query->withCount($withCount))
            ->addColumn('name', function($channel) {
                return $channel->name;
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%".strtolower($keyword)."%"]);
            })->toJson();
    }

    public function exportSummary(Request $request)
    {
        $type = $request->query('type');
        $filters = $request->except(['_token', 'type']);

        switch ($type) {
            case 'saas_app':
                return Excel::download(
                    new SaasAppSummaryExport($filters),
                    'saas-app-summary-'.now()->format('Y-m-d').'.xlsx'
                );
            case 'topic':
                return Excel::download(
                    new TopicSummaryExport($filters),
                    'topic-summary-'.now()->format('Y-m-d').'.xlsx'
                );
            case 'mno':
                return Excel::download(
                    new MnoSummaryExport($filters),
                    'mno-summary-'.now()->format('Y-m-d').'.xlsx'
                );
            case 'payment_channel':
                return Excel::download(
                    new PaymentChannelSummaryExport($filters),
                    'payment-channel-summary-'.now()->format('Y-m-d').'.xlsx'
                );
            case 'all_report':
                return Excel::download(
                    new AllReportExport($filters),
                    'all-report-'.now()->format('Y-m-d').'.xlsx'
                );
            case 'ticket_list_by_mno':
                return Excel::download(
                    new AllReportExport($filters),
                    'ticket-list-by-mno-'.now()->format('Y-m-d').'.xlsx'
                );
            default:
                abort(404, 'Invalid export type');
        }
    }

    public function data(Request $request)
    {
        $tickets = Ticket::with(['client', 'assignedTo', 'topic', 'subtopic', 'tertiaryTopic', 'operators', 'sender', 'paymentChannel', 'saasApp'])
            ->when($request->start_date, function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($request->client_id, function($q) use ($request) {
                $q->where('client_id', $request->client_id);
            })
            ->when($request->assigned_to, function($q) use ($request) {
                $q->where('assigned_to', $request->assigned_to);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', Str::lower($request->status));
            })
            ->when($request->topic_id, function($q) use ($request) {
                $q->where('topic_id', $request->topic_id);
            })
            ->when($request->subtopic_id, function($q) use ($request) {
                $q->where('sub_topic_id', $request->subtopic_id);
            })
            ->when($request->tertiary_topic_id, function($q) use ($request) {
                $q->where('tertiary_topic_id', $request->tertiary_topic_id);
            })
            ->when($request->priority, function($q) use ($request) {
                $q->where('priority', $request->priority);
            })
            ->when($request->mno, function($q) use ($request) {
                $q->whereHas('operators', function($query) use ($request) {
                    $query->where('operators.id', $request->mno);
                });
            })
            ->when($request->payment_channel, function($q) use ($request) {
                $q->where('payment_channel_id', $request->payment_channel);
            })
            ->when($request->sender_id, function($q) use ($request) {
                $q->where('sender_id', $request->sender_id);
            })
            ->when($request->saas_app, function($q) use ($request) {
                $q->where('saas_app_id', $request->saas_app);
            })
            ->select('tickets.*')
            ->addSelect([
                'topic_path' => function($query) {
                    $query->selectRaw("CONCAT_WS(' -> ',
                        (SELECT name FROM topics WHERE id = tickets.topic_id),
                        (SELECT name FROM sub_topics WHERE id = tickets.sub_topic_id),
                        (SELECT name FROM tertiary_topics WHERE id = tickets.tertiary_topic_id)
                    )");
                }
            ]);

        return datatables()->eloquent($tickets)
            ->addIndexColumn()
            ->make(true);
    }

    public function export(Request $request)
    {
        $tickets = Ticket::with(['client', 'assignedTo', 'topic', 'subtopic', 'tertiaryTopic', 'operators', 'sender', 'paymentChannel', 'saasApp'])
            ->when($request->start_date, function($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->end_date);
            })
            ->when($request->client_id, function($q) use ($request) {
                $q->where('client_id', $request->client_id);
            })
            ->when($request->assigned_to, function($q) use ($request) {
                $q->where('assigned_to', $request->assigned_to);
            })
            ->when($request->status, function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->topic_id, function($q) use ($request) {
                $q->where('topic_id', $request->topic_id);
            })
            ->when($request->subtopic_id, function($q) use ($request) {
                $q->where('sub_topic_id', $request->subtopic_id);
            })
            ->when($request->tertiary_topic_id, function($q) use ($request) {
                $q->where('tertiary_topic_id', $request->tertiary_topic_id);
            })
            ->when($request->priority, function($q) use ($request) {
                $q->where('priority', $request->priority);
            })
            ->when($request->mno, function($q) use ($request) {
                $q->whereHas('operators', function($query) use ($request) {
                    $query->where('operators.id', $request->mno);
                });
            })
            ->when($request->payment_channel, function($q) use ($request) {
                $q->where('payment_channel_id', $request->payment_channel);
            })
            ->when($request->sender_id, function($q) use ($request) {
                $q->where('sender_id', $request->sender_id);
            })
            ->when($request->saas_app, function($q) use ($request) {
                $q->where('saas_app_id', $request->saas_app);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('pdf.ticket_report', [
            'page-width' => '300mm', // wider page
            'zoom' => 0.8,
            'tickets' => $tickets,
            'filters' => $request->all()
        ]);

        return $pdf->download('ticket_report_'.now()->format('Ymd_His').'.pdf');
    }
}
