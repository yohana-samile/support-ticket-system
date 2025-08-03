<?php

namespace App\Notifications;

use App\Models\Ticket\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUnassignedNotification extends Notification implements ShouldQueue
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
            ->subject('Ticket Unassigned: ' . $this->ticket->title)
            ->line('You have been unassigned from a ticket.')
            ->line('Ticket ID: ' . $this->ticket->uid)
            ->line('Title: ' . $this->ticket->title);
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => 'You have been unassigned from a ticket',
            'url' => route('ticket.show', $this->ticket->id),
        ];
    }
}
