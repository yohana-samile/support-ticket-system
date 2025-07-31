<?php

namespace App\Exports\Tickets;

use App\Models\SaasApp;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SaasAppSummaryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    protected $statuses;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
        $this->statuses = Status::orderBy('name')->get();
    }

    public function collection()
    {
        $query = SaasApp::withCount(['tickets']);

        // Apply filters
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

        foreach ($this->statuses as $status) {
            $query->withCount([
                "tickets as {$status->slug}_count" => function($q) use ($status) {
                    $q->where('status', $status->slug);
                }
            ]);
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        $headings = ['SAAS Application', 'Total Tickets'];

        foreach ($this->statuses as $status) {
            $headings[] = $status->name;
        }

        return $headings;
    }

    public function map($saasApp): array
    {
        $row = [
            $saasApp->abbreviation,
            $saasApp->tickets_count,
        ];

        foreach ($this->statuses as $status) {
            $count = $saasApp->{"{$status->slug}_count"} ?? 0;
            $row[] = $count;
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
