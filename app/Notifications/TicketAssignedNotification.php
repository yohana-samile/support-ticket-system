<?php

namespace App\Notifications;

use App\Models\Ticket\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TicketAssignedNotification  extends Notification implements ShouldQueue
{
    use Queueable;

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
        return (new MailMessage)
            ->subject('New Ticket Assigned: ' . $this->ticket->title)
            ->line('You have been assigned a new ticket.')
            ->action('Resolve Ticket', URL::signedRoute('backend.ticket.resolve.via.email', $this->ticket->uid))
            ->line('Ticket ID: ' . $this->ticket->ticket_number)
            ->line('Priority: ' . ucfirst($this->ticket->priority));
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'message' => 'You have been assigned a new ticket',
            'url' => route('backend.ticket.show', $this->ticket->uid),
        ];
    }
}
