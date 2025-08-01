<?php

namespace App\Exports\Tickets\Topic;

use App\Models\Status;
use App\Models\Topic;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TicketTopicExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $statuses = Status::all();
        $query = Topic::query()->with('subtopics');

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
            'Topic Name',
            'Subtopics',
            'Total Tickets'
        ], $statuses);
    }

    public function map($topic): array
    {
        $statusCounts = [];
        foreach (Status::orderBy('name')->get() as $status) {
            $statusCounts[] = $topic->{"{$status->slug}_tickets_count"};
        }

        return array_merge([
            $topic->name,
            $topic->subtopics->pluck('name')->join(', '),
            $topic->tickets_count
        ], $statusCounts);
    }

    public function title(): string
    {
        return 'Topics Summary';
    }
}
