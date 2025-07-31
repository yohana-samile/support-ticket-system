<?php

namespace App\Exports\Tickets;

use App\Repositories\Backend\TicketRepository;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class AllReportExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $filters;
    protected $ticketRepository;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->ticketRepository = app(TicketRepository::class);
    }

    public function query()
    {
        $query = $this->ticketRepository->all();

        // Date filtering
        if (!empty($this->filters['start_date'])) {
            $query->whereHas('tickets', function($q) {
                $q->where('tickets.created_at', '>=', $this->filters['start_date']);
            });
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereHas('tickets', function($q) {
                $q->where('tickets.created_at', '<=', $this->filters['end_date']);
            });
        }
        return $query;
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'saas',
            'Client',
            'Title',
            'Topic',
            'Status',
            'Priority',
            'Assigned To',
            'Reported At',
            'Resolved At',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_number,
            $ticket->saasApp->abbreviation ?? 'N/A',
            $ticket->client->name ?? 'N/A',
            $ticket->title,
            $ticket->topic->name ?? 'N/A',
            ucfirst($ticket->status),
            ucfirst($ticket->priority),
            $ticket->assignedTo->name ?? 'Unassigned',
            $ticket->created_at->format('Y-m-d H:i:s'),
            $ticket->time_solved->format('Y-m-d H:i:s') ?? 'N/A'
        ];
    }

    public function title(): string
    {
        return 'Tickets Report';
    }
}
