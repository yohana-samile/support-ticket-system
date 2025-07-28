<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Access\User;
use App\Models\Operator;
use App\Models\PaymentChannel;
use App\Models\Status;
use App\Models\System\Code;
use App\Models\System\CodeValue;
use App\Models\Ticket\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        $codeId = Code::query()->where('name', 'Ticket Priority')->value('id');
        $data['staff'] = User::query()->where('is_active', true)->orderBy('name')->get();
        $data['priorities'] = CodeValue::query()->where('code_id', $codeId)->get();
        $data['statues'] = Status::orderBy('name')->get();

        $data['mnos'] = Operator::orderBy('name')->get();
        $data['paymentChannels'] = PaymentChannel::orderBy('name')->get();
        return view("pages.backend.report.index", $data);
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
