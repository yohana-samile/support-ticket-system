<?php

namespace App\Exports\Tickets\Client;

use App\Models\Access\Client;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TicketClientExportSummary implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $filters;
    protected $statuses;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        $this->statuses = Status::orderBy('name')->get();
    }

    public function query()
    {
        $withCount = $this->buildWithCountArray();

        $query = Client::query()
            ->with(['saasApp'])
            ->withCount($withCount)
            ->has('tickets'); // Only include clients with tickets

        $this->applyDateFilters($query);

        return $query->orderBy('name');
    }

    protected function buildWithCountArray(): array
    {
        $withCount = ['tickets'];

        foreach ($this->statuses as $status) {
            $withCount["tickets as {$status->slug}_tickets_count"] = function($q) use ($status) {
                $q->where('tickets.status', $status->slug);
            };
        }

        return $withCount;
    }

    protected function applyDateFilters($query): void
    {
        if (!empty($this->filters['start_date'])) {
            $query->whereHas('tickets', function($q) {
                $q->where('created_at', '>=', $this->filters['start_date']);
            });
        }

        if (!empty($this->filters['end_date'])) {
            $query->whereHas('tickets', function($q) {
                $q->where('created_at', '<=', $this->filters['end_date']);
            });
        }
    }

    public function headings(): array
    {
        $statusHeadings = $this->statuses->pluck('name')->toArray();

        return array_merge([
            'Client Name',
            'Saas App',
            'Total Tickets'
        ], $statusHeadings);
    }

    public function map($client): array
    {
        $statusCounts = $this->statuses->map(function ($status) use ($client) {
            return $client->{"{$status->slug}_tickets_count"} ?? 0;
        })->toArray();

        return array_merge([
            $client->name,
            $client->saasApp->name ?? 'N/A',
            $client->tickets_count
        ], $statusCounts);
    }

    public function title(): string
    {
        return 'Client Ticket Summary';
    }
}
