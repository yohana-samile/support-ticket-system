<?php

namespace App\Exports\Tickets;

use App\Models\Operator;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class MnoSummaryExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $statuses = Status::all();
        $query = Operator::query();

        $withCount = ['tickets'];
        foreach ($statuses as $status) {
            $withCount["tickets as {$status->slug}_tickets_count"] = function($q) use ($status) {
                $q->where('tickets.status', $status->slug);
            };
        }

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

        return $query->withCount($withCount)->orderBy('name');
    }

    public function headings(): array
    {
        $statuses = Status::orderBy('name')->pluck('name')->toArray();

        return array_merge([
            'MNO Name',
            'Total Tickets'
        ], $statuses);
    }

    public function map($mno): array
    {
        $statusCounts = [];
        foreach (Status::orderBy('name')->get() as $status) {
            $statusCounts[] = $mno->{"{$status->slug}_tickets_count"};
        }

        return array_merge([
            $mno->name,
            $mno->tickets_count
        ], $statusCounts);
    }

    public function title(): string
    {
        return 'MNOs Summary';
    }
}
