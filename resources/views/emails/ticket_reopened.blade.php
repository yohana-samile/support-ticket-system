<!DOCTYPE html>
<html>
<head>
    <title>Ticket Reopened: {{ $ticket->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .ticket-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 5px;
        }
        .ticket-header {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 5px 5px 0 0;
            margin-bottom: 20px;
            color: #856404;
        }
        .ticket-details {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .ticket-details td {
            padding: 8px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }
        .ticket-details td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .ticket-description {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
        .reopened-note {
            background-color: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="ticket-container">
    <div class="ticket-header">
        <h2>⚠️ Ticket Reopened: {{ $ticket->title }}</h2>
    </div>

    <div class="reopened-note">
        The customer has reopened this ticket, indicating their issue hasn't been fully resolved.
    </div>

    <p>Hello {{ $notifiable->name }},</p>
    <p><strong>Please review the reopened ticket:</strong></p>

    <table class="ticket-details">
        <tr>
            <td>Ticket ID:</td>
            <td><a href="{{ route('backend.ticket.show', $ticket->uid) }}">#{{ $ticket->ticket_number }}</a></td>
        </tr>
        <tr>
            <td>Service:</td>
            <td>{{ $saasApp ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Client:</td>
            <td>{{ $client ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Priority:</td>
            <td>{{ ucfirst($ticket->priority) }}</td>
        </tr>
        <tr>
            <td>Topic:</td>
            <td>{{ $ticketAbout ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Created:</td>
            <td>{{ $createdAt }} ({{ $timeSinceCreation }})</td>
        </tr>
        @if($ticket->sender_id)
            <tr>
                <td>Sender ID:</td>
                <td>{{ $ticket->sender->sender_id }}</td>
            </tr>
            <tr>
                <td>Mobile Operators:</td>
                <td>{{ $operators ?? 'N/A' }}</td>
            </tr>
        @endif
        @if($ticket->payment_channel_id)
            <tr>
                <td>Payment Channel:</td>
                <td>{{ $ticket->paymentChannel->name }}</td>
            </tr>
        @endif
        <tr>
            <td>Reopened At:</td>
            <td>{{ now()->format('M d, Y H:i') }}</td>
        </tr>
    </table>

    @if($ticket->description)
        <p><strong>Original Description:</strong></p>
        <div class="ticket-description">
            {!! nl2br(e($ticket->description)) !!}
        </div>
    @endif

    @if($ticket->latestComment)
        <p><strong>Customer's Reopening Comment:</strong></p>
        <div class="ticket-description">
            {!! nl2br(e($ticket->latestComment->comment)) !!}
        </div>
    @endif

    <a href="{{ URL::signedRoute('backend.ticket.resolve.via.email', $ticket->uid) }}" class="action-button">
        Resolve
    </a>

    <div class="footer">
        <p>Please address the customer's concerns promptly.</p>
    </div>
</div>
</body>
</html>
