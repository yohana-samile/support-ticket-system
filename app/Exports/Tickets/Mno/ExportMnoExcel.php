<?php

namespace App\Exports\Tickets\Mno;

use App\Models\Operator;
use App\Models\Ticket\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportMnoExcel implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    protected $mno;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        if ($filters['scope'] === 'current' && isset($filters['operator'])) {
            $this->mno = Operator::where('uid', $filters['operator'])->firstOrFail();
        }
    }

    public function collection()
    {
        $query = Ticket::with(['topic', 'user', 'assignedTo', 'saasApp', 'client'])
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

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'Saas',
            'Client',
            'Subject',
            'Topic',
            'Mno',
            'Status',
            'Priority',
            'Created By',
            'Assignee',
            'Created At',
            'Updated At',
            'Resolution Time (hours)'
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_number,
            $ticket->saasApp->name,
            $ticket->client->name,
            $ticket->title,
            $ticket->topic->name ?? 'N/A',
            $ticket->operators->pluck('name')->join(', '),
            $ticket->status,
            $ticket->priority,
            $ticket->user->name ?? 'N/A',
            $ticket->assignedTo->name ?? 'Unassigned',
            $ticket->created_at->format('Y-m-d H:i:s'),
            $ticket->updated_at->format('Y-m-d H:i:s'),
            $ticket->time_solved ? round($ticket->time_solved->diffInHours($ticket->created_at), 2) : 'N/A'
        ];
    }

    public function title(): string
    {
        return 'Tickets by mno';
    }
}
