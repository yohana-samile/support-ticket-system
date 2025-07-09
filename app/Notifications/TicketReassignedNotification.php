<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReassignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ticket Assigned: ' . $this->ticket->title)
            ->line('You have been assigned a ticket.')
            ->action('View Ticket', route('tickets.show', $this->ticket->id))
            ->line('Ticket ID: ' . $this->ticket->uid)
            ->line('Priority: ' . ucfirst($this->ticket->priority));
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => 'You have been assigned a ticket',
            'url' => route('tickets.show', $this->ticket->id),
        ];
    }
}
