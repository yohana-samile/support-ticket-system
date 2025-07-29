<?php

namespace App\Notifications;

use App\Models\Ticket\Ticket;
use App\Traits\EmailTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable, EmailTrait;

    public $ticket;
    public $sendMail;

    public function __construct(Ticket $ticket, $sendMail = true)
    {
        $this->ticket = $ticket;
        $this->sendMail = $sendMail;
    }

    public function via($notifiable)
    {
        return $this->sendMail ? ['mail', 'database'] : ['database'];
    }

    public function toMail($notifiable)
    {
        $ticket = $this->ticket;
        $saasApp = optional($ticket->saasApp)->abbreviation ?? 'N/A';
        $client = optional($ticket->client)->name ?? 'N/A';
        $priority = $this->formatPriority($ticket->priority);
        $ticketAbout = $this->getTicketCategoryPath($ticket);
        $createdAt = $ticket->created_at->format('M j, Y g:i A');
        $timeSinceCreation = $ticket->created_at->diffForHumans();
        $operators = $ticket->operators->pluck('name')->join(', ') ?: null;

        return (new MailMessage)
            ->subject("{$priority} Ticket Assigned: {$ticket->title}")
            ->view('emails.ticket_assigned', [
                'notifiable' => $notifiable,
                'ticket' => $ticket,
                'saasApp' => $saasApp,
                'client' => $client,
                'priority' => $priority,
                'ticketAbout' => $ticketAbout,
                'createdAt' => $createdAt,
                'timeSinceCreation' => $timeSinceCreation,
                'operators' => $operators,
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'priority' => $this->ticket->priority,
            'service' => optional($this->ticket->saasApp)->name,
            'client' => optional($this->ticket->client)->name,
            'category' => $this->getTicketCategoryPath($this->ticket, ' → '),
            'message' => 'You have been assigned a new ticket',
            'url' => route('backend.ticket.show', $this->ticket->uid),
        ];
    }
}
