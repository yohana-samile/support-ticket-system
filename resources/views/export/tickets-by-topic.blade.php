<!DOCTYPE html>
<html>
<head>
    <title>Tickets by Topic</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="header">
    <h1>Tickets by Topic</h1>
</div>

<div class="info">
    <p><strong>Title:</strong> {{ $title }}</p>
    <p><strong>Date Range:</strong>
        {{ $startDate ? $startDate : 'Start' }} to {{ $endDate ? $endDate : 'End' }}</p>
    <p><strong>Generated On:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
</div>

<table>
    <thead>
    <tr>
        <th>Ticket ID</th>
        @isset($mno)
            <th>MNO</th>
        @endisset
        @isset($channel)
            <th>Payment channel</th>
        @endisset
        <th>Saas</th>
        <th>Client</th>
        <th>Subject</th>
        <th>Topic</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Created By</th>
        <th>Assignee</th>
        <th>Created At</th>
        <th>Updated At</th>
        <th>Resolution Time</th>
    </tr>
    </thead>
    <tbody>
    @foreach($tickets as $ticket)
        <tr>
            <td>{{ $ticket->ticket_number }}</td>
            @isset($mno)
                <td>{{ $ticket->operators->pluck('name')->join(', ') }}</td>
            @endisset
            @isset($channel)
                <td>{{ $ticket->paymentChannel->name ?? 'N/A' }}</td>
            @endisset
            <td>{{ $ticket->saasApp->name }}</td>
            <td>{{ $ticket->client->name }}</td>
            <td>{{ $ticket->title }}</td>
            <td>{{ $ticket->topic->name ?? 'N/A' }}</td>
            <td>{{ $ticket->status }}</td>
            <td>{{ $ticket->priority }}</td>
            <td>{{ $ticket->user->name ?? 'N/A' }}</td>
            <td>{{ $ticket->assignedTo->name ?? 'Unassigned' }}</td>
            <td>{{ $ticket->created_at->format('Y-m-d H:i:s') }}</td>
            <td>{{ $ticket->updated_at->format('Y-m-d H:i:s') }}</td>
            <td>{{ $ticket->time_solved ? round($ticket->time_solved->diffInHours($ticket->created_at), 2) : 'N/A' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
