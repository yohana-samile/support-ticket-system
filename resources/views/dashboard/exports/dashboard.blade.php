<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket System Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0 0; color: #666; }
        .section { margin-bottom: 30px; }
        .section-title {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-left: 4px solid #4e73df;
            margin-bottom: 15px;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 12px; }
        .text-right { text-align: right; }
        .filters { margin-bottom: 20px; }
        .filter-item { margin-right: 10px; display: inline-block; }
    </style>
</head>
<body>
<div class="header">
    <h1>Ticket System Report</h1>
    <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
</div>

@if(!empty($filters))
    <div class="section filters">
        <h3>Applied Filters</h3>
        <div>
            @if(!empty($filters['payment_channel']))
                <span class="filter-item"><strong>Payment Channel:</strong> {{ $filters['payment_channel'] }}</span>
            @endif
            @if(!empty($filters['mobile_operator']))
                <span class="filter-item"><strong>Mobile Operator:</strong> {{ $filters['mobile_operator'] }}</span>
            @endif
            @if(!empty($filters['saas_app']))
                <span class="filter-item"><strong>SaaS App:</strong> {{ $filters['saas_app'] }}</span>
            @endif
            @if(!empty($filters['status']))
                <span class="filter-item"><strong>Status:</strong> {{ ucfirst($filters['status']) }}</span>
            @endif
        </div>
    </div>
@endif

<div class="section">
    <div class="section-title">
        <h3>Summary Statistics</h3>
    </div>
    <table>
        <tr>
            <th>Total Tickets</th>
            <td>{{ $totalTickets }}</td>
        </tr>
        <tr>
            <th>Open Tickets</th>
            <td>{{ $openTickets }}</td>
        </tr>
        <tr>
            <th>Resolved Tickets</th>
            <td>{{ $resolvedTickets }}</td>
        </tr>
        <tr>
            <th>Reopened Tickets</th>
            <td>{{ $reopenedCount }}</td>
        </tr>
    </table>
</div>

@if($recentTickets->count() > 0)
    <div class="section">
        <div class="section-title">
            <h3>Recent Tickets</h3>
        </div>
        <table>
            <thead>
            <tr>
                <th>Ticket #</th>
                <th>Title</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Created</th>
            </tr>
            </thead>
            <tbody>
            @foreach($recentTickets as $ticket)
                <tr>
                    <td>{{ $ticket->ticket_number }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>{{ ucfirst($ticket->status) }}</td>
                    <td>{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
                    <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
</body>
</html>
