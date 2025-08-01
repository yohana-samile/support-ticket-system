<?php

namespace App\Exports\Tickets\Saas;

use App\Exports\SharedFunctions;
use App\Models\SaasApp;
use App\Models\Ticket\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportSaasAppPdf
{
    use SharedFunctions;
    protected $filters;
    protected $saas;
    public $chunkSize = 500;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        if ($filters['scope'] === 'current' && isset($filters['saas_app_id'])) {
            $this->saas = SaasApp::where('uid', $filters['saas_app_id'])->firstOrFail();
        }
    }

    public function download()
    {
        $query = Ticket::with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'assignedTo'])
            ->when($this->filters['scope'] === 'current', function ($query) {
                $query->where('saas_app_id', $this->saas->id);
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
            'title' => isset($this->filters['saas']) ? $this->saas->name : 'All Saa Applications'
        ]);

        return $pdf->download($filename);
    }

}
