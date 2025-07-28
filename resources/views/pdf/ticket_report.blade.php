<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1200px">
    <title>Ticket Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { font-size: 14px; color: #666; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
            word-wrap: break-word;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }
        th { background-color: #f2f2f2; }
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 12px; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-dark { background-color: #343a40; color: white; }
        .filters { margin-bottom: 20px; }
        .filter-item { margin-bottom: 5px; }
        .filter-label { font-weight: bold; display: inline-block; width: 120px; }
    </style>
</head>
<body>
<div class="header">
    <div class="title">Ticket Report</div>
    <div class="subtitle">Generated on: {{ now()->format('Y-m-d H:i:s') }}</div>
</div>

@if(!empty($filters))
    <div class="filters">
        <div class="filter-item"><span class="filter-label">Date Range:</span>
            {{ $filters['start_date'] ?? '' }} to {{ $filters['end_date'] ?? '' }}
        </div>
        @if(!empty($filters['client_id']))
            <div class="filter-item"><span class="filter-label">Client:</span>
                {{ $tickets->first()->client->name ?? '' }}
            </div>
        @endif
        @if(!empty($filters['assigned_to']))
            <div class="filter-item"><span class="filter-label">Assigned To:</span>
                {{ $tickets->first()->assignedTo->name ?? '' }}
            </div>
        @endif
        @if(!empty($filters['status']))
            <div class="filter-item"><span class="filter-label">Status:</span>
                {{ ucfirst($filters['status']) }}
            </div>
        @endif
        @if(!empty($filters['topic_id']))
            <div class="filter-item"><span class="filter-label">Topic:</span>
                {{ $tickets->first()->topic->name ?? '' }}
            </div>
        @endif
        @if(!empty($filters['subtopic_id']))
            <div class="filter-item"><span class="filter-label">Subtopic:</span>
                {{ $tickets->first()->subtopic->name ?? '' }}
            </div>
        @endif
        @if(!empty($filters['tertiary_topic_id']))
            <div class="filter-item"><span class="filter-label">Tertiary Topic:</span>
                {{ $tickets->first()->tertiaryTopic->name ?? '' }}
            </div>
        @endif
        @if(!empty($filters['priority']))
            <div class="filter-item"><span class="filter-label">Priority:</span>
                {{ ucfirst($filters['priority']) }}
            </div>
        @endif
    </div>
@endif

<div style="overflow-x: auto;">
    <table>
        <thead>
        <tr>
            <th>Ticket ID</th>
            <th>saas_app</th>
            <th>Client</th>
            <th>Subject</th>
            <th>Topic</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Payment Channel</th>
            <th>Mno</th>
            <th>sender_id</th>
            <th>Assigned To</th>
            <th>Created At</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->ticket_number }}</td>
                <td>{{ $ticket->saasApp->abbreviation ?? '' }}</td>
                <td>{{ $ticket->client->name ?? '' }}</td>
                <td>{{ $ticket->title }}</td>
                <td>
                    {{ $ticket->topic->name ?? '' }}
                    @if($ticket->subtopic)
                        → {{ $ticket->subtopic->name }}
                    @endif
                    @if($ticket->tertiaryTopic)
                        → {{ $ticket->tertiaryTopic->name }}
                    @endif
                </td>
                <td>
                        <span class="badge
                            @if($ticket->status === 'open') badge-primary
                            @elseif($ticket->status === 'resolved') badge-success
                            @else badge-secondary
                            @endif">
                            {{ ucfirst($ticket->status) }}
                        </span>
                </td>
                <td>
                        <span class="badge
                            @if($ticket->priority === 'low') badge-info
                            @elseif($ticket->priority === 'medium') badge-warning
                            @elseif($ticket->priority === 'high') badge-danger
                            @elseif($ticket->priority === 'critical') badge-dark
                            @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                </td>
                <td>{{ $ticket->paymentChannel->name ?? '' }}</td>
                <td>{{ $ticket->operators->pluck('name')->join(', ') ?? '' }}</td>
                <td>{{ $ticket->sender->sender_id ?? '' }}</td>
                <td>{{ $ticket->assignedTo->name ?? '' }}</td>
                <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
