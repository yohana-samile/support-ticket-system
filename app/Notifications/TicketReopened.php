<?php

namespace App\Notifications;

use App\Models\Ticket\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReopened extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        if ($notifiable->notifications()
            ->where('type', self::class)
            ->where('created_at', '>', now()->subHours(1))
            ->exists()) {
            return [];
        }
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticketUrl = route('backend.ticket.show', $this->ticket->uid);

        return (new MailMessage)
            ->subject("[Ticket Reopened] #{$this->ticket->ticket_number}: {$this->ticket->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The following ticket has been reopened by the customer:")
            ->line("Ticket #: {$this->ticket->ticket_number}")
            ->line("Title: {$this->ticket->title}")
            ->line("Priority: " . ucfirst($this->ticket->priority))
            ->action('View Ticket', $ticketUrl)
            ->line("Please review the ticket and take appropriate action.")
            ->line('Thank you for your attention!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'message' => 'A ticket has been reopened and requires your attention',
            'link' => route('backend.ticket.show', $this->ticket->uid),
        ];
    }

    public function viaQueues(): array
    {
        return [
            'mail' => 'mail-queue',
            'database' => 'database-queue',
        ];
    }
}
