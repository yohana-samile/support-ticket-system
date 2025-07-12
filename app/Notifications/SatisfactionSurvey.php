<?php

namespace App\Notifications;

use App\Models\Ticket\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SatisfactionSurvey extends Notification implements ShouldQueue
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
        $url = route('frontend.ticket.feedback', $this->ticket->uid);
        return (new MailMessage)
            ->subject("📝 Satisfaction Survey for Ticket #{$this->ticket->ticket_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("We've marked your ticket as resolved. Please let us know if your issue was fully resolved.")
            ->line("Ticket #: {$this->ticket->ticket_number}")
            ->line("Title: {$this->ticket->title}")
            ->action('Complete Survey', $url)
            ->line('Thank you for helping us improve our service!')
            ->salutation('Best regards,');
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => 'Please provide feedback on your recent ticket',
            'link' => route('frontend.ticket.feedback', $this->ticket->uid),
        ];
    }
}
