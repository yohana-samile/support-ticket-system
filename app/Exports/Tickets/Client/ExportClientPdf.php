<?php

namespace App\Exports\Tickets\Client;

use App\Exports\SharedFunctions;
use App\Models\Access\Client;
use App\Models\Ticket\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportClientPdf
{
    use SharedFunctions;
    protected $filters;
    protected $client;
    public $chunkSize = 500;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        if ($filters['scope'] === 'current' && isset($filters['client'])) {
            $this->client = Client::where('uid', $filters['client'])->firstOrFail();
        }
    }

    public function download()
    {
        $query = Ticket::with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'assignedTo'])
            ->when($this->filters['scope'] === 'current', function ($query) {
                $query->where('client_id', $this->client->id);
            })
            ->when($this->filters['start_date'] && $this->filters['scope'] === 'current', function ($query) {
                $query->whereDate('created_at', '>=', $this->filters['start_date']);
            })
            ->when($this->filters['end_date'] && $this->filters['scope'] === 'current', function ($query) {
                $query->whereDate('created_at', '<=', $this->filters['end_date']);
            })
            ->orderBy('created_at', 'desc');

        $tickets = $this->filters['scope'] === 'all'
            ? $query->lazy($this->chunkSize)
            : $query->get();
        $filename = $this->generateFilename();

        $pdf = PDF::loadView('export.tickets-by-topic', [
            'tickets' => $tickets,
            'scope' => $this->filters['scope'],
            'startDate' => $this->filters['start_date'] ?? null,
            'endDate' => $this->filters['end_date'] ?? null,
            'title' => isset($this->filters['client']) ? $this->client->name : 'All Clients'
        ]);

        return $pdf->download($filename);
    }
}
