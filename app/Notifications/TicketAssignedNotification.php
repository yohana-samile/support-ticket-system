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
        $ticket = $this->ticket;

        $topic = optional($ticket->topic)->name;
        $subtopic = optional($ticket->subtopic)->name;
        $tertiaryTopic = optional($ticket->tertiaryTopic)->name;

        $ticketAbout = implode(' -> ', array_filter([$topic, $subtopic, $tertiaryTopic]));

        return (new MailMessage)
            ->subject('New Ticket Assigned: ' . $ticket->title)
            ->line('Priority: ' . ucfirst($ticket->priority))
            ->line('About: ' . $ticketAbout)
            ->line('Message: ' . $ticket->description)
            ->line('Ticket ID: ' . $ticket->ticket_number)
            ->action('Resolve Ticket', URL::signedRoute('backend.ticket.resolve.via.email', $ticket->uid));
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
