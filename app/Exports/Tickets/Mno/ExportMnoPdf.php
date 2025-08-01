<?php

namespace App\Exports\Tickets\Mno;

use App\Exports\SharedFunctions;
use App\Models\Operator;
use App\Models\SaasApp;
use App\Models\Ticket\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportMnoPdf
{
    use SharedFunctions;
    protected $filters;
    protected $mno;
    public $chunkSize = 500;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        if ($filters['scope'] === 'current' && isset($filters['operator'])) {
            $this->mno = Operator::where('uid', $filters['operator'])->firstOrFail();
        }
    }

    public function download()
    {
        $query = Ticket::with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'assignedTo'])
            ->when($this->filters['scope'] === 'current', function ($query) {
                $query->join('ticket_operator', 'tickets.id', '=', 'ticket_operator.ticket_id')->where('ticket_operator.operator_id', $this->mno->id);
            })
            ->when($this->filters['start_date'] && $this->filters['scope'] === 'current', function ($query) {
                $query->whereDate('created_at', '>=', $this->filters['start_date']);
            })
            ->when($this->filters['end_date'] && $this->filters['scope'] === 'current', function ($query) {
                $query->whereDate('created_at', '<=', $this->filters['end_date']);
            })
            ->orderBy('created_at', 'desc')->select('tickets.*');

        $tickets = $this->filters['scope'] === 'all'
            ? $query->lazy($this->chunkSize)
            : $query->get();
        $filename = $this->generateFilename();

        $pdf = PDF::loadView('export.tickets-by-topic', [
            'tickets' => $tickets,
            'scope' => $this->filters['scope'],
            'startDate' => $this->filters['start_date'] ?? null,
            'endDate' => $this->filters['end_date'] ?? null,
            'title' => isset($this->filters['operator']) ? $this->mno->name : 'All Mno',
            'mno' => 'mno'
        ]);

        return $pdf->download($filename);
    }

}
