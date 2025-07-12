<?php

namespace App\Notifications;

use App\Models\Ticket\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
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
            ->subject('Ticket Created: ' . $this->ticket->title)
            ->line('Your ticket has been successfully created.')
            ->action('View Ticket', route('backend.ticket.show', $this->ticket->uid))
            ->line('Ticket ID: ' . $this->ticket->ticket_number)
            ->line('We will notify you when there are updates.');
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => 'Your ticket has been created',
            'url' => route('backend.ticket.show', $this->ticket->uid),
        ];
    }
}
