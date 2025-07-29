<?php

namespace App\Notifications;

use App\Models\Ticket\Ticket;
use App\Traits\EmailTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReopened extends Notification implements ShouldQueue
{
    use Queueable, EmailTrait;

    public function __construct(public Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        /**
        // Prevent duplicate notifications within 1 hour
        if ($notifiable->notifications()
            ->where('type', self::class)
            ->where('created_at', '>', now()->subHours(1))
            ->exists()) {
            return [];
        }
        */
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $saasApp = $this->ticket->saasApp->name ?? 'N/A';
        $client = $this->ticket->client->name ?? 'N/A';
        $createdAt = $this->ticket->created_at->format('M d, Y H:i');
        $timeSinceCreation = $this->ticket->created_at->diffForHumans();
        $ticketAbout = $this->getTicketCategoryPath($this->ticket);

        return (new MailMessage)
            ->subject("[Reopened] #{$this->ticket->ticket_number}: {$this->ticket->title}")
            ->markdown('emails.ticket_reopened', [
                'ticket' => $this->ticket,
                'notifiable' => $notifiable,
                'saasApp' => $saasApp,
                'client' => $client,
                'priority' => ucfirst($this->ticket->priority),
                'ticketAbout' => $ticketAbout,
                'createdAt' => $createdAt,
                'timeSinceCreation' => $timeSinceCreation,
                'operators' => $this->ticket->sender->operators ?? 'N/A',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'title' => $this->ticket->title,
            'priority' => $this->ticket->priority,
            'message' => 'A ticket has been reopened and requires your attention',
            'link' => route('backend.ticket.show', $this->ticket->uid),
            'client_name' => $this->ticket->client->name ?? 'Unknown',
            'reopened_at' => now()->toDateTimeString(),
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
