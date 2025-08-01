<?php

namespace App\Exports\Tickets\Channel;

use App\Exports\SharedFunctions;
use App\Models\PaymentChannel;
use App\Models\Ticket\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportPaymentChannelPdf
{
    use SharedFunctions;
    protected $filters;
    protected $channel;
    public $chunkSize = 500;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        if ($filters['scope'] === 'current' && isset($filters['channel'])) {
            $this->channel = PaymentChannel::where('uid', $filters['channel'])->firstOrFail();
        }
    }

    public function download()
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
            'title' => isset($this->filters['channel']) ? $this->channel->name : 'All Payment Channels',
            'channel' => 'channel'
        ]);

        return $pdf->download($filename);
    }

}
