<?php

namespace App\Exports\Tickets\Channel;

use App\Models\PaymentChannel;
use App\Models\Ticket\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportPaymentChannelExcel implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    protected $channel;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        if ($filters['scope'] === 'current' && isset($filters['channel'])) {
            $this->channel = PaymentChannel::where('uid', $filters['channel'])->firstOrFail();
        }
    }

    public function collection()
    {
        $query = Ticket::query()
            ->when($this->filters['scope'] === 'current', function ($query) {
                $query->where('payment_channel_id', $this->channel->id);
            })
            ->when($this->filters['start_date'] && $this->filters['scope'] === 'current', function ($query) {
                $query->whereDate('created_at', '>=', $this->filters['start_date']);
            })
            ->when($this->filters['end_date'] && $this->filters['scope'] === 'current', function ($query) {
                $query->whereDate('created_at', '<=', $this->filters['end_date']);
            })->orderBy('created_at', 'desc');

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Ticket ID',
            'Channel',
            'Saas',
            'Client',
            'Subject',
            'Topic',
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
            $ticket->paymentChannel->name,
            $ticket->saasApp->name,
            $ticket->client->name,
            $ticket->title,
            $ticket->topic->name ?? 'N/A',
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
        return 'Tickets by channel';
    }
}
