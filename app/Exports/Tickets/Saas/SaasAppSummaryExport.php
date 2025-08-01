<?php

namespace App\Exports\Tickets\Saas;

use App\Models\SaasApp;
use App\Models\Status;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yajra\DataTables\DataTables;

class SaasAppSummaryExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;
    protected $statuses;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
        $this->statuses = Status::orderBy('name')->get();
    }

    public function saasAppData(Request $request)
    {
        $statuses = Status::all();
        $query = SaasApp::query();

        $withCount = ['tickets'];
        foreach ($statuses as $status) {
            $withCount["tickets as {$status->slug}_tickets_count"] = function($q) use ($status) {
                $q->where('status', $status->slug);
            };
        }

        // Date filtering
        if ($request->start_date) {
            $query->whereHas('tickets', function($q) use ($request) {
                $q->where('created_at', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $query->whereHas('tickets', function($q) use ($request) {
                $q->where('created_at', '<=', $request->end_date);
            });
        }

        // Handle search
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchTerm = strtolower($request->search['value']);

            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                    ->orWhereRaw('LOWER(abbreviation) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        // Handle sorting
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            // Only allow sorting by name (column 0)
            if ($orderColumn == 0) {
                $query->orderBy('name', $orderDirection);
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        return DataTables::of($query->withCount($withCount))
            ->addColumn('name', function($saasApp) {
                return $saasApp->name;
            })
            ->addColumn('abbreviation', function($saasApp) {
                return $saasApp->abbreviation;
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%".strtolower($keyword)."%"]);
            })->toJson();
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
