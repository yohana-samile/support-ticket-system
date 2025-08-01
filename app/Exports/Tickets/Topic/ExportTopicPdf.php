<?php

namespace App\Exports\Tickets\Topic;

use App\Models\Ticket\Ticket;
use App\Models\Topic;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportTopicPdf
{
    protected $filters;
    protected $topic;
    public $chunkSize = 500;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        if ($filters['scope'] === 'current' && isset($filters['topic_id'])) {
            $this->topic = Topic::where('uid', $filters['topic_id'])->firstOrFail();
        }
    }

    public function download()
    {
        $query = Ticket::with(['topic', 'subtopic', 'tertiaryTopic', 'client', 'user', 'assignedTo'])
            ->when($this->filters['scope'] === 'current', function ($query) {
                $query->where('topic_id', $this->topic->id);
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
            'topic' => isset($this->filters['topic']) ? $this->topic->name : 'All Topics'
        ]);

        return $pdf->download($filename);
    }

    protected function generateFilename(): string
    {
        $prefix = $this->filters['scope'] === 'all'
            ? 'all-tickets'
            : 'tickets-' . ($this->topic->uid ?? 'filtered');

        return sprintf(
            '%s-%s.pdf',
            $prefix,
            now()->format('Y-m-d-H-i-s')
        );
    }
}
